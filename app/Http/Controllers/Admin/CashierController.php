<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierController extends Controller
{
    /**
     * Menampilkan halaman Kasir (POS + Antrean).
     */
    public function index(): View
    {
        $categories = Category::query()
            ->with(['menuItems' => fn ($q) => $q->available()])
            ->get();

        $groups = $categories
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $category->menuItems->map(fn (MenuItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => (int) (float) $item->price,
                    'image' => $item->image,
                ])->values(),
            ])
            ->values();

        return view('admin.cashier', [
            'categories' => $groups,
            'taxRate' => Order::TAX_RATE,
        ]);
    }

    /**
     * Memproses pesanan kasir (offline) dan langsung menandai lunas (tunai).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'amount_paid' => ['nullable', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $lineItems = $this->resolveLineItems($data['items']);

        $subtotal = collect($lineItems)->sum(fn (array $line) => $line['price'] * $line['quantity']);
        $tax = (int) round($subtotal * Order::TAX_RATE);
        $total = $subtotal + $tax;

        $order = DB::transaction(function () use ($data, $lineItems, $total) {
            $order = Order::create([
                'customer_name' => $data['customer_name'] ?: 'Pelanggan',
                'customer_phone' => $data['customer_phone'] ?? null,
                'table_number' => trim((string) ($data['table_number'] ?? '')) ?: null,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
                'payment_method' => Order::METHOD_CASH,
                'payment_status' => Order::STATUS_SUCCESS,
                'amount_paid' => $data['amount_paid'] ?? $total,
                'total_price' => $total,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create($line);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Pesanan berhasil disimpan.',
            'order_id' => $order->id,
        ]);
    }

    /**
     * Mengambil pesanan terbaru (pending/success) untuk antrean kasir.
     *
     * Tanpa filter tanggal ketat agar pesanan landing page selalu tampil,
     * lalu ambil batch terbaru sebagai fallback aman lintas hari.
     */
    public function fetchActiveOrders(): JsonResponse
    {
        $orders = Order::query()
            ->with(['items' => fn ($q) => $q->with('menuItem')])
            ->whereIn('payment_status', [Order::STATUS_PENDING, Order::STATUS_SUCCESS])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'table_number' => $order->table_number,
                'notes' => $order->notes,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'total' => $order->total_formatted,
                'time' => $order->created_at->setTimezone('Asia/Jakarta')->format('H:i'),
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->menuItem?->name ?? 'Item',
                    'quantity' => $item->quantity,
                    'price' => 'Rp '.number_format((float) $item->price, 0, ',', '.'),
                ])->values(),
            ])
            ->values();

        return response()->json($orders);
    }

    /**
     * Memvalidasi & meresolusi baris item dari input keranjang.
     */
    private function resolveLineItems(array $requestItems): array
    {
        $menus = MenuItem::query()
            ->whereIn('id', collect($requestItems)->pluck('menu_item_id'))
            ->available()
            ->get()
            ->keyBy('id');

        return collect($requestItems)
            ->map(function (array $line) use ($menus) {
                abort_unless($menus->has($line['menu_item_id']), 422, 'Beberapa menu tidak tersedia.');

                $menu = $menus[$line['menu_item_id']];

                return [
                    'menu_item_id' => $menu->id,
                    'quantity' => $line['quantity'],
                    'price' => $menu->price,
                ];
            })
            ->values()
            ->all();
    }
}
