<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-semibold text-coffee-900">Dashboard</h2>
        <p class="mt-1 text-sm text-coffee-500">Ringkasan performa restoran untuk hari ini.</p>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Menu Aktif</p>
                <span class="grid h-9 w-9 place-items-center rounded-cozy bg-sage-100 text-sage-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['menu_active']) }}</p>
            <p class="mt-1 text-xs text-coffee-500">dari {{ number_format($stats['menu_total']) }} total menu</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Pesanan Hari Ini</p>
                <span class="grid h-9 w-9 place-items-center rounded-cozy bg-terracotta-300/30 text-terracotta-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['orders_today']) }}</p>
            <p class="mt-1 text-xs text-coffee-500">
                pendapatan <span class="font-semibold text-coffee-800">Rp {{ number_format($stats['orders_today_revenue'], 0, ',', '.') }}</span>
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Pesanan Pending</p>
                <span class="grid h-9 w-9 place-items-center rounded-cozy bg-amber-100 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['orders_pending']) }}</p>
            <p class="mt-1 text-xs text-coffee-500">menunggu pembayaran online</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Promo Aktif</p>
                <span class="grid h-9 w-9 place-items-center rounded-cozy bg-cream-200 text-coffee-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['promotions_active']) }}</p>
            <p class="mt-1 text-xs text-coffee-500">promosi sedang berjalan</p>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Kategori</p>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['categories']) }}</p>
            <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block text-sm font-semibold text-terracotta-500 hover:text-terracotta-600">Kelola kategori →</a>
        </div>
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Total Menu</p>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['menu_total']) }}</p>
            <a href="{{ route('admin.menu.index') }}" class="mt-3 inline-block text-sm font-semibold text-terracotta-500 hover:text-terracotta-600">Kelola menu →</a>
        </div>
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Foto Galeri</p>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['gallery_photos']) }}</p>
            <a href="{{ route('admin.galeri.index') }}" class="mt-3 inline-block text-sm font-semibold text-terracotta-500 hover:text-terracotta-600">Kelola galeri →</a>
        </div>
        <div class="card p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-coffee-400">Reservasi Pending</p>
            <p class="mt-3 font-serif text-3xl font-semibold text-coffee-900">{{ number_format($stats['reservations_pending']) }}</p>
            <a href="{{ route('admin.reservations.index') }}?status=pending" class="mt-3 inline-block text-sm font-semibold text-terracotta-500 hover:text-terracotta-600">Kelola reservasi →</a>
        </div>
    </div>
</x-admin-layout>