<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — '.config('app.name') : config('app.name').' — Admin' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700&family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-coffee-800 antialiased">
        @php
            $nav = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'home'],
                ['label' => 'Kasir & Antrean', 'route' => 'admin.kasir', 'pattern' => 'admin.kasir', 'icon' => 'cashier'],
                ['label' => 'Kelola Meja', 'route' => 'admin.dining-tables.index', 'pattern' => 'admin.dining-tables.*', 'icon' => 'table'],
                ['label' => 'Reservasi', 'route' => 'admin.reservations.index', 'pattern' => 'admin.reservations.*', 'icon' => 'calendar'],
                ['label' => 'Kelola Kategori', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'icon' => 'tag'],
                ['label' => 'Kelola Menu', 'route' => 'admin.menu.index', 'pattern' => 'admin.menu.*', 'icon' => 'menu'],
                ['label' => 'Kelola Promo', 'route' => 'admin.promo.index', 'pattern' => 'admin.promo.*', 'icon' => 'promo'],
                ['label' => 'Galeri', 'route' => 'admin.galeri.index', 'pattern' => 'admin.galeri.*', 'icon' => 'gallery'],
                ['label' => 'Profil Resto', 'route' => 'admin.profil', 'pattern' => 'admin.profil*', 'icon' => 'store'],
            ];

            $icons = [
                'home' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'cashier' => 'M3 10h18M7 15h3M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z',
                'tag' => 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V5a2 2 0 012-2z',
                'menu' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
                'promo' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
                'gallery' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                'store' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'table' => 'M9 21V9m0 0V6.5a1.5 1.5 0 011.5-1.5h3A1.5 1.5 0 0115 6.5V9m0 0v12m-9 0h18',
                'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
            ];
        @endphp

        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-cream-100">
            {{-- Topbar mobile --}}
            <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-coffee-100 bg-cream-50/95 px-4 py-3 backdrop-blur lg:hidden">
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    aria-label="Buka menu navigasi"
                    class="rounded-cozy p-2 text-coffee-800 transition hover:bg-cream-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="font-serif text-lg font-semibold text-coffee-900">{{ config('app.name') }} Admin</span>
            </header>

            {{-- Overlay mobile --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-coffee-900/60 backdrop-blur-sm lg:hidden"
                style="display: none"
            ></div>

            {{-- Sidebar --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col overflow-y-auto bg-coffee-900 transition-transform duration-300 lg:translate-x-0"
            >
                {{-- Brand --}}
                <div class="flex items-center gap-3 border-b border-coffee-800 px-6 py-5">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-cozy bg-terracotta-500 font-serif text-lg font-bold text-cream-50 shadow-warm">
                        R
                    </div>
                    <div class="leading-tight">
                        <p class="font-serif text-lg font-semibold text-cream-50">{{ config('app.name') }}</p>
                        <p class="text-[11px] uppercase tracking-widest text-coffee-400">Panel Admin</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 py-4">
                    <ul class="space-y-1">
                        @foreach ($nav as $item)
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="{{ request()->routeIs($item['pattern']) ? 'bg-terracotta-500 text-cream-50 shadow-warm' : 'text-cream-200 hover:bg-coffee-800 hover:text-cream-50' }} flex items-center gap-3 rounded-cozy px-3.5 py-2.5 text-sm font-medium transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{!! $icons[$item['icon']] !!}"/></svg>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- User + Logout --}}
                <div class="border-t border-coffee-800 px-6 py-4">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-sage-500 font-bold text-coffee-900">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 leading-tight">
                            <p class="truncate text-sm font-semibold text-cream-50">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-coffee-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-cozy border border-coffee-700 px-4 py-2.5 text-sm font-medium text-cream-200 transition hover:border-terracotta-500 hover:bg-terracotta-500 hover:text-cream-50"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Content --}}
            <div class="lg:pl-72">
                <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
                    @isset($header)
                        <header class="mb-6">
                            {{ $header }}
                        </header>
                    @endisset

                    @if (session('status'))
                        <div class="mb-6 flex items-start gap-2 rounded-cozy border-l-4 border-sage-400 bg-sage-100 px-4 py-3 text-sm text-coffee-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-sage-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <div class="mb-6 rounded-cozy border-l-4 border-terracotta-500 bg-terracotta-300/20 px-4 py-3 text-sm text-coffee-800">
                            <p class="font-semibold">Mohon perbaiki beberapa hal berikut:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <footer class="px-6 pb-8 text-center text-xs text-coffee-400">
                    © {{ date('Y') }} RLS — Panel Manajemen Konten
                </footer>
            </div>
        </div>
    </body>
</html>