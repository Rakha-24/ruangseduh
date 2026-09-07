<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Checkout — {{ config('app.name', 'Ruang Seduh') }}</title>

        <!-- Fonts: Playfair Display (serif) & Inter (sans-serif) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700&family=inter:300,400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Midtrans Snap.js -->
        @if (config('midtrans.is_production'))
            <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        @else
            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        @endif
    </head>
    <body class="font-sans text-coffee-800 antialiased">

        <div class="min-h-screen bg-cream-100">
            {{-- Header --}}
            <header class="border-b border-coffee-100/60 bg-cream-100/95 backdrop-blur">
                <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-5 sm:px-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="grid h-9 w-9 place-items-center rounded-cozy bg-terracotta-500 font-serif font-bold text-cream-50">R</span>
                        <span class="font-serif text-xl font-bold text-coffee-900">Ruang<span class="text-terracotta-500">Seduh</span></span>
                    </a>
                    <span class="rounded-full bg-sage-100 px-4 py-1.5 text-sm font-semibold text-sage-600">Checkout</span>
                </div>
            </header>

            <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
                <h1 class="section-title">Selesaikan Pembayaran</h1>
                <p class="mt-2 text-coffee-600">Periksa kembali pesananmu sebelum melakukan pembayaran.</p>

                <div class="mt-8 grid gap-6">
                    {{-- Ringkasan Pesanan --}}
                    <div class="card bg-cream-50 p-6">
                        <h2 class="font-serif text-lg font-bold text-coffee-900">Ringkasan Pesanan</h2>

                        <ul class="mt-4 divide-y divide-coffee-100">
                            @foreach ($order->items as $item)
                                <li class="flex items-center justify-between gap-4 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-coffee-800">{{ $item->menuItem->name ?? 'Item' }} &times; {{ $item->quantity }}</p>
                                        <p class="text-sm text-coffee-500">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="whitespace-nowrap font-medium text-coffee-800">Rp {{ number_format((float) $item->price * $item->quantity, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 flex items-center justify-between border-t border-coffee-100 pt-4">
                            <span class="font-serif font-bold text-coffee-900">Total</span>
                            <span class="font-serif text-2xl font-bold text-terracotta-500">{{ $order->total_formatted }}</span>
                        </div>
                    </div>

                    {{-- Data Pelanggan --}}
                    <div class="card bg-cream-50 p-6">
                        <h2 class="font-serif text-lg font-bold text-coffee-900">Data Pelanggan</h2>
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-coffee-500">Nama</dt>
                                <dd class="font-medium text-coffee-800">{{ $order->customer_name }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-coffee-500">No. HP</dt>
                                <dd class="font-medium text-coffee-800">{{ $order->customer_phone }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-coffee-500">Status</dt>
                                <dd class="font-medium text-sage-600 capitalize">{{ $order->payment_status }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Pembayaran --}}
                    <div class="card bg-cream-50 p-6">
                        <h2 class="font-serif text-lg font-bold text-coffee-900">Metode Pembayaran</h2>
                        <p class="mt-2 text-sm text-coffee-600">
                            Kami mendukung pembayaran melalui Virtual Account, QRIS, E-Wallet, dan Kartu Kredit/Debit.
                        </p>

                        <button type="button" id="pay-button"
                                class="btn-primary mt-6 w-full text-base">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                const snapToken = @json($snapToken);

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        window.location.href = '/redirect-checkout?status=success&order_id=' + result.order_id;
                    },
                    onPending: function (result) {
                        window.location.href = '/redirect-checkout?status=pending&order_id=' + result.order_id;
                    },
                    onError: function (result) {
                        window.location.href = '/redirect-checkout?status=error&order_id=' + result.order_id;
                    },
                    onClose: function () {
                        alert('Pembayaran dibatalkan. Silakan coba lagi.');
                    }
                });
            });
        </script>
    </body>
</html>