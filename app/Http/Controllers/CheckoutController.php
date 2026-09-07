<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * Mapping status transaksi Midtrans → payment_status di tabel orders.
     */
    private const TRANSACTION_STATUS_MAP = [
        'settlement' => Order::STATUS_SUCCESS,
        'capture' => Order::STATUS_SUCCESS,
        'acceptance' => Order::STATUS_SUCCESS,
        'pending' => Order::STATUS_PENDING,
        'deny' => Order::STATUS_FAILED,
        'failure' => Order::STATUS_FAILED,
        'cancel' => Order::STATUS_FAILED,
        'expire' => Order::STATUS_EXPIRED,
    ];

    /**
     * Inisialisasi konfigurasi global Midtrans.
     */
    private function initMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Menyimpan pesanan dari keranjang publik & generate Snap token.
     */
    public function store(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $cart = $this->buildCart($request);

        if ($cart->isEmpty()) {
            return redirect()->route('home')->with(
                'status',
                'Keranjang belanja masih kosong. Silakan tambahkan menu terlebih dahulu.'
            );
        }

        $customerName = trim($validated['customer_name']);
        $tableNumber = isset($validated['table_number']) ? trim($validated['table_number']) : null;
        $notes = isset($validated['notes']) ? trim($validated['notes']) : null;

        $total = $cart->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = null;
        $snapToken = null;

        try {
            // Transaksi menyeluruh: order + order_items tersimpan SEBELUM Snap token.
            // Bila Midtrans gagal, seluruh transaksi di-rollback (tidak ada order yatim).
            [$order, $snapToken] = DB::transaction(function () use ($cart, $customerName, $tableNumber, $notes, $total) {
                $order = Order::create([
                    'customer_name' => $customerName,
                    'table_number' => $tableNumber ?: null,
                    'notes' => $notes ?: null,
                    'total_price' => $total,
                    'payment_status' => Order::STATUS_PENDING,
                ]);

                foreach ($cart as $item) {
                    $order->items()->create([
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                $token = $this->generateSnapToken($order, $cart->all());

                $order->update(['snap_token' => $token]);

                return [$order, $token];
            });
        } catch (\Throwable $e) {
            Log::error('Midtrans token generation failed', [
                'order_id' => $order?->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('home')->with(
                'status',
                'Gagal menghubungi payment gateway (Midtrans). Pesan: '.$e->getMessage()
                .' — Periksa MIDTRANS_SERVER_KEY / MIDTRANS_CLIENT_KEY pada file .env.'
            );
        }

        return view('checkout', ['order' => $order, 'snapToken' => $snapToken]);
    }

    /**
     * Menangani redirect dari callback Snap.js (setelah popup ditutup/berhasil).
     */
    public function callback(Request $request): RedirectResponse
    {
        $messages = [
            'success' => 'Pembayaran berhasil! Terima kasih atas pesananmu.',
            'pending' => 'Pembayaran tertunda. Silakan selesaikan pembayaranmu.',
            'error' => 'Pembayaran gagal. Silakan coba lagi.',
        ];

        $status = $request->query('status', 'error');

        return redirect()->route('home')->with('status', $messages[$status] ?? $messages['error']);
    }

    /**
     * Menangani HTTP POST webhook dari Midtrans dan memperbarui status pesanan.
     */
    public function notification(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        if (! $this->verifySignature($payload)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $order = Order::find($payload['order_id'] ?? null);

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $status = $this->mapStatus($payload['transaction_status'] ?? null);

        $order->update(['payment_status' => $status]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Memverifikasi signature key Midtrans (SHA-512).
     */
    private function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.config('midtrans.server_key'));

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Memetakan transaction_status Midtrans ke payment_status lokal.
     */
    private function mapStatus(?string $transactionStatus): string
    {
        if ($transactionStatus && isset(self::TRANSACTION_STATUS_MAP[$transactionStatus])) {
            return self::TRANSACTION_STATUS_MAP[$transactionStatus];
        }

        return Order::STATUS_PENDING;
    }

    /**
     * Men-generate Snap token dari parameter transaksi.
     */
    private function generateSnapToken(Order $order, array $cart): string
    {
        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => collect($cart)->map(fn ($item) => [
                'id' => $item['menu_item_id'] ?? $item['name'],
                'price' => (int) $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ])->values()->all(),
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => '080000000000',
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Membangun keranjang dari request POST publik (Kollect).
     *
     * Data dikirim sebagai JSON: [ { menu_item_id, name, price, quantity }, ... ].
     * Barang tanpa menu_item_id yang valid tetap dapat dipesan sebagai item ad-hoc.
     */
    private function buildCart(Request $request): Collection
    {
        $raw = $request->input('cart');

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (! is_array($raw)) {
            $raw = [];
        }

        $items = collect($raw)
            ->filter(fn ($item) => isset($item['name']) && isset($item['price']) && (int) ($item['quantity'] ?? 0) > 0)
            ->values();

        if ($items->isNotEmpty()) {
            $mapped = $items->map(function ($item) {
                $menuItemId = isset($item['menu_item_id']) ? (int) $item['menu_item_id'] : null;

                return [
                    'menu_item_id' => $menuItemId,
                    'name' => (string) $item['name'],
                    'price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                ];
            });

            // Hanya pertahankan menu_item_id yang benar-benar ada di tabel menu_items.
            // Barang dummy/ad-hoc (mis. dari keranjang landing page) disimpan tanpa relasi FK.
            $validIds = MenuItem::query()
                ->whereIn('id', $mapped->pluck('menu_item_id')->filter()->all())
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            return $mapped->map(function ($item) use ($validIds) {
                $item['menu_item_id'] = in_array($item['menu_item_id'], $validIds, true)
                    ? $item['menu_item_id']
                    : null;

                return $item;
            });
        }

        return collect($this->dummyCart());
    }

    /**
     * Keranjang dummy untuk uji coba checkout (fallback bila menu kosong).
     */
    private function dummyCart(): array
    {
        $items = MenuItem::query()->limit(2)->get();

        if ($items->isNotEmpty()) {
            return $items->map(fn (MenuItem $item) => [
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => 1,
            ])->values()->all();
        }

        return [
            ['menu_item_id' => null, 'name' => 'Espresso', 'price' => 18000, 'quantity' => 2],
            ['menu_item_id' => null, 'name' => 'Nasi Goreng Spesial', 'price' => 28000, 'quantity' => 1],
        ];
    }
}
