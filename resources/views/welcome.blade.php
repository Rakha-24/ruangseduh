<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $profile->about_text ?? 'Ruang Seduh — tempat hangat untuk berkumpul dan menikmati hidangan.' }}">

        <title>{{ config('app.name', 'Ruang Seduh') }}</title>

        {{-- Fonts: Playfair Display (heading) + Plus Jakarta Sans (body) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        {{-- Tailwind CSS via CDN (tanpa kompilasi npm) --}}
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: { cream: '#FDFBF7', terracotta: '#E07A5F', sage: '#81B29A', dark: '#3D405B' },
                        fontFamily: {
                            serif: ['Playfair Display', 'Georgia', 'serif'],
                            sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    }
                }
            }
        </script>

        {{-- Alpine.js via CDN untuk Customer Cart --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>

    <body class="min-h-screen bg-cream font-sans text-gray-600 antialiased" x-data="customerCart()">

        {{-- Notice session (status dari checkout / callback pembayaran) --}}
        @if (session('status'))
            <div class="fixed inset-x-0 top-4 z-[100] px-4" x-data="{ show: true }" x-show="show" x-transition x-cloak>
                <div class="mx-auto flex max-w-md items-center gap-3 rounded-2xl border border-terracotta/20 bg-white px-5 py-4 shadow-xl">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-terracotta/10 text-terracotta">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </span>
                    <p class="flex-1 text-sm font-medium text-gray-800">{{ session('status') }}</p>
                    <button type="button" @click="show = false" class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif

        @php
            // ================= Data & Dummy (dijamin minimal 6 kartu, gambar selalu Unsplash) =================
            $coffeeImg = [
                'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80',
                'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&q=80',
                'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=800&q=80',
                'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=800&q=80',
                'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80',
                'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&q=80',
            ];

            $menuItems = collect();
            $idx = 0;
            foreach ($categories as $category) {
                foreach ($category->menuItems as $item) {
                    $img = ($item->image && ! str_contains($item->image, 'placehold.co'))
                        ? $item->image
                        : $coffeeImg[$idx % count($coffeeImg)];
                    $menuItems->push((object) [
                        'id' => $item->id ?? ($idx + 1),
                        'name' => $item->name,
                        'description' => $item->description ?? '',
                        'price' => (float) ($item->price ?? 0),
                        'price_label' => $item->formatted_price ?? 'Rp ' . number_format((float) $item->price, 0, ',', '.'),
                        'category' => $category->name,
                        'image' => $img,
                    ]);
                    $idx++;
                }
            }

            $menuDummies = [
                (object) ['id' => ($idx + 1), 'name' => 'Kopi Susu Gula Aren', 'description' => 'Espresso manis dengan gula aren asli dan susu segar yang creamy.', 'price' => 27000, 'price_label' => 'Rp 27.000', 'category' => 'Kopi & Susu', 'image' => $coffeeImg[2]],
                (object) ['id' => ($idx + 2), 'name' => 'Matcha Latte', 'description' => 'Serbuk matcha premium dikocok dengan susu steamed yang lembut.', 'price' => 33000, 'price_label' => 'Rp 33.000', 'category' => 'Kopi & Susu', 'image' => $coffeeImg[3]],
                (object) ['id' => ($idx + 3), 'name' => 'Avocado Toast', 'description' => 'Sourdough panggang, alpukat segar, telur, dan taburan biji wijen.', 'price' => 39000, 'price_label' => 'Rp 39.000', 'category' => 'Sarapan', 'image' => $coffeeImg[4]],
                (object) ['id' => ($idx + 4), 'name' => 'Buttermilk Pancake', 'description' => 'Tiga lapis pancake lembut dengan madu asli dan buah segar.', 'price' => 35000, 'price_label' => 'Rp 35.000', 'category' => 'Sarapan', 'image' => $coffeeImg[5]],
            ];
            $k = 0;
            while ($menuItems->count() < 6 && $k < count($menuDummies)) {
                $menuItems->push($menuDummies[$k++]);
            }

            // Galeri: gambar interior Unsplash, masonry asimetris.
            $interiorImg = [
                'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&q=80',
                'https://images.unsplash.com/photo-1521017432531-fbd92d768814?w=800&q=80',
                'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&q=80',
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80',
                'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&q=80',
                'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=800&q=80',
            ];
            $captions = ['Area bar hangat', 'Latte art signature', 'Sudut nyaman bersantai', 'Interior terang & cozy', 'Meja makan premium', 'Secangkir di sore hari'];

            $photos = collect();
            $m = 0;
            foreach ($gallery as $photo) {
                $photos->push((object) [
                    'image' => $interiorImg[$m % count($interiorImg)],
                    'caption' => $photo->caption ?? $captions[$m % count($captions)],
                ]);
                $m++;
            }
            while ($photos->count() < 6) {
                $photos->push((object) [
                    'image' => $interiorImg[$photos->count()],
                    'caption' => $captions[$photos->count()],
                ]);
            }

            $galleryAspects = ['aspect-[4/5]', 'aspect-square', 'aspect-[4/3]', 'aspect-[3/4]', 'aspect-square', 'aspect-[4/3]'];
        @endphp

        {{-- ============ NAVBAR (Glassmorphism, fixed) ============ --}}
        <header id="beranda" class="fixed top-0 z-50 w-full border-b border-gray-100 bg-white/80 backdrop-blur-md">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="#beranda" class="flex items-center gap-2.5">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-terracotta font-serif text-xl font-semibold text-white">R</span>
                    <span class="font-serif text-2xl font-semibold tracking-tight text-gray-900">Ruang<span class="text-terracotta">Seduh</span></span>
                </a>

                <div class="hidden items-center gap-8 md:flex">
                    <a href="#beranda" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">Beranda</a>
                    <a href="#tentang" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">Tentang</a>
                    <a href="#menu" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">Menu</a>
                    <a href="#galeri" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">Galeri</a>
                    <a href="#kontak" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">Kontak</a>
                </div>

                <div class="hidden md:block">
                    <button type="button"
                            @click="$dispatch('open-reservation')"
                            class="rounded-full bg-terracotta px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-terracotta/90 hover:shadow-md">
                        Pesan Meja
                    </button>
                </div>

                <button id="nav-toggle" class="grid h-10 w-10 place-items-center rounded-xl border border-gray-200 text-gray-700 md:hidden" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </nav>

            <div id="mobile-menu" class="hidden border-t border-gray-100 bg-white/95 px-4 pb-5 pt-2 md:hidden">
                <a href="#beranda" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Beranda</a>
                <a href="#tentang" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Tentang</a>
                <a href="#menu" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Menu</a>
                <a href="#galeri" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Galeri</a>
                <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank" rel="noopener" class="mt-3 block rounded-full bg-white/10 px-6 py-3 text-center text-sm font-semibold text-white ring-1 ring-white/20">Chat WhatsApp</a>
                <button type="button" @click="$dispatch('open-reservation'); document.getElementById('mobile-menu').classList.add('hidden')"
                        class="mt-2 block rounded-full bg-terracotta px-6 py-3 text-center text-sm font-semibold text-white">Pesan Meja</button>
            </div>
        </header>

        <main>

            {{-- ============ HERO (asimetris, teks kiri) ============ --}}
            <section class="relative flex min-h-screen items-center overflow-hidden">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1600&q=80"
                     alt="Suasana interior Ruang Seduh"
                     class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-transparent"></div>

                <div class="relative z-10 mx-auto w-full max-w-7xl px-4 pb-24 pt-40 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-terracotta backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-terracotta"></span>
                            Warm & Cozy Since 2019
                        </span>

                        <h1 class="mt-7 font-serif text-5xl font-semibold leading-[1.05] text-white md:text-7xl">
                            Tempat hangat untuk
                            <span class="italic text-terracotta">berkumpul</span>,
                            bekerja &amp; menikmati
                        </h1>

                        <p class="mt-6 max-w-xl text-base leading-relaxed text-gray-200 sm:text-lg">
                            {{ $profile->about_text }}
                        </p>

                        <div class="mt-10 flex flex-col items-start gap-4 sm:flex-row">
                            <a href="#menu" class="rounded-full bg-terracotta px-8 py-4 text-sm font-semibold text-white shadow-lg transition hover:bg-terracotta/90 hover:shadow-xl">
                                Jelajahi Menu
                            </a>
                            <a href="#galeri" class="rounded-full border border-white/30 bg-white/5 px-8 py-4 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">
                                Lihat Galeri
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Meta info bawah hero --}}
                <div class="absolute inset-x-0 bottom-0 z-10 hidden border-t border-white/10 bg-black/30 backdrop-blur md:block">
                    <div class="mx-auto grid max-w-7xl grid-cols-3 gap-6 px-4 py-5 text-sm text-gray-200 sm:px-6 lg:px-8">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-400">Alamat</p>
                            <p class="mt-1 font-medium text-gray-100">{{ $profile->address }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-400">Jam Buka</p>
                            <p class="mt-1 font-medium text-gray-100">
                                {{ $profile->opening_hours['monday']['open'] ?? '08.00' }} – {{ $profile->opening_hours['monday']['close'] ?? '22.00' }} (Senin – Jumat)
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-400">Kontak</p>
                            <p class="mt-1 font-medium text-gray-100">{{ $profile->phone }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============ TENTANG (kolase organik) ============ --}}
            <section id="tentang" class="scroll-mt-28 py-20 md:py-28">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid items-center gap-16 lg:grid-cols-2">
                        <div class="relative">
                            <div class="absolute -left-8 -top-8 h-40 w-40 rounded-full bg-sage/20"></div>
                            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&q=80"
                                 alt="Suasana hangat di dalam Ruang Seduh"
                                 class="relative z-10 h-[28rem] w-full rounded-[2rem] object-cover shadow-xl">
                            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&q=80"
                                 alt="Secangkir kopi hangat"
                                 class="absolute -bottom-8 -right-4 z-20 hidden h-44 w-40 rounded-3xl border-8 border-cream object-cover shadow-2xl sm:block">
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-terracotta">Tentang Kami</span>
                            <h2 class="mt-4 font-serif text-4xl font-semibold leading-tight text-gray-900 md:text-5xl">
                                Diracik dengan hati,
                                <span class="italic text-sage">diseduh dengan cerita</span>
                            </h2>
                            <p class="mt-6 max-w-xl leading-relaxed text-gray-600">
                                {{ $profile->about_text }}
                            </p>
                            <p class="mt-4 max-w-xl leading-relaxed text-gray-600">
                                Dari racikan kopi spesialti hingga menu rumahan yang hangat, setiap sudut kami rancang untuk jadi rumah kedua bagi siapa pun yang singgah.
                            </p>

                            <div class="mt-10 flex flex-wrap items-center gap-10">
                                <div>
                                    <p class="font-serif text-3xl font-semibold text-gray-900">6+</p>
                                    <p class="mt-1 text-sm text-gray-500">Tahun melayani</p>
                                </div>
                                <div>
                                    <p class="font-serif text-3xl font-semibold text-gray-900">15k</p>
                                    <p class="mt-1 text-sm text-gray-500">Pelanggan setia</p>
                                </div>
                                <div>
                                    <p class="font-serif text-3xl font-semibold text-gray-900">30+</p>
                                    <p class="mt-1 text-sm text-gray-500">Menu pilihan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============ MENU ============ --}}
            <section id="menu" class="scroll-mt-28 bg-white py-20 md:py-28">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-terracotta">Menu Kami</span>
                            <h2 class="mt-4 font-serif text-4xl font-semibold leading-tight text-gray-900 md:text-5xl">
                                Favorit yang selalu
                                <span class="italic text-terracotta">hangat</span>
                            </h2>
                        </div>
                        <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank" rel="noopener" class="group inline-flex items-center gap-2 text-sm font-semibold text-gray-900 transition hover:text-terracotta">
                            Lihat semua menu
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>

                    <div class="mt-14 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($menuItems as $item)
                            <article class="group overflow-hidden rounded-2xl border border-gray-50 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                                <div class="relative h-56 overflow-hidden">
                                    <img src="{{ $item->image }}"
                                         alt="{{ $item->name }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-medium text-gray-700 backdrop-blur">{{ $item->category }}</span>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="font-serif text-xl font-semibold text-gray-900">{{ $item->name }}</h3>
                                        <span class="whitespace-nowrap font-semibold text-terracotta">{{ $item->price_label }}</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $item->description }}</p>
                                    <button type="button"
                                            @click="addToCart({ id: {{ $item->id }}, name: '{{ $item->name }}', price: {{ $item->price }}, image: '{{ $item->image }}' })"
                                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-terracotta px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-terracotta/90 hover:shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ============ GALERI (masonry asimetris, dark section) ============ --}}
            <section id="galeri" class="scroll-mt-28 bg-dark py-20 md:py-28">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-terracotta">Galeri</span>
                            <h2 class="mt-4 font-serif text-4xl font-semibold leading-tight text-white md:text-5xl">
                                Momen dari sudut
                                <span class="italic text-sage">ruang kami</span>
                            </h2>
                        </div>
                        <p class="max-w-sm text-sm leading-relaxed text-gray-400">
                            Sekilas suasana hangat yang menanti kehadiranmu — dari racikan di balik bar sampai sudut favorit para tamu.
                        </p>
                    </div>

                    <div class="mt-14 columns-1 gap-6 sm:columns-2 lg:columns-3">
                        @foreach ($photos as $i => $photo)
                            <figure class="group relative mb-6 break-inside-avoid overflow-hidden rounded-2xl shadow-lg">
                                <div class="{{ $galleryAspects[$i % count($galleryAspects)] }} overflow-hidden">
                                    <img src="{{ $photo->image }}"
                                         alt="{{ $photo->caption }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                </div>
                                <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent px-5 pb-5 pt-12 text-sm font-medium text-white/90 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ $photo->caption }}
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ============ CTA ============ --}}
            <section id="kontak" class="bg-terracotta py-20 md:py-24">
                <div class="mx-auto flex max-w-7xl flex-col items-start gap-8 px-4 sm:px-6 lg:px-8 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="font-serif text-3xl font-semibold leading-tight text-white md:text-5xl">
                            Siap menghangatkan
                            <span class="italic">harimu?</span>
                        </h2>
                        <p class="mt-4 max-w-xl leading-relaxed text-white/80">
                            Pesan meja atau tanya-tanya dulu — kami siap menyambutmu dengan kopi dan senyuman.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank" rel="noopener"
                       class="shrink-0 rounded-full bg-white px-9 py-4 text-sm font-semibold text-dark shadow-lg transition hover:bg-cream hover:shadow-xl">
                        Chat via WhatsApp
                    </a>
                </div>
            </section>
        </main>

        {{-- ============ FOOTER ============ --}}
        <footer class="scroll-mt-0 bg-dark pb-10 pt-16 text-gray-400">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 md:grid-cols-3">
                    <div>
                        <a href="#beranda" class="flex items-center gap-2.5">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-terracotta font-serif text-xl font-semibold text-white">R</span>
                            <span class="font-serif text-2xl font-semibold text-white">Ruang<span class="text-terracotta">Seduh</span></span>
                        </a>
                        <p class="mt-5 max-w-xs text-sm leading-relaxed">
                            {{ $profile->about_text }}
                        </p>
                    </div>

                    <div>
                        <h3 class="font-serif text-lg font-semibold text-white">Kunjungi Kami</h3>
                        <div class="mt-5 space-y-3 text-sm leading-relaxed">
                            <p class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-terracotta" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                {{ $profile->address }}
                            </p>
                            <p class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-terracotta" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $profile->phone }}
                            </p>
                            <p class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-terracotta" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                                <a href="mailto:{{ $profile->email }}" class="transition hover:text-terracotta">{{ $profile->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-serif text-lg font-semibold text-white">Jam Buka</h3>
                        <div class="mt-5 space-y-2.5 text-sm">
                            <p class="flex justify-between border-b border-white/10 pb-2"><span>Senin – Jumat</span><span class="text-gray-200">{{ $profile->opening_hours['monday']['open'] ?? '08.00' }} – {{ $profile->opening_hours['monday']['close'] ?? '22.00' }}</span></p>
                            <p class="flex justify-between border-b border-white/10 pb-2"><span>Sabtu – Minggu</span><span class="text-gray-200">{{ $profile->opening_hours['saturday']['open'] ?? '08.00' }} – {{ $profile->opening_hours['saturday']['close'] ?? '23.00' }}</span></p>
                            <div class="flex items-center gap-4 pt-3">
                                <a href="https://instagram.com/{{ $profile->instagram }}" target="_blank" rel="noopener" aria-label="Instagram" class="text-gray-400 transition hover:text-terracotta">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41C15.58 21.83 15.2 21.84 12 21.84s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 1.8c-3.14 0-3.5.01-4.74.07-.9.04-1.38.19-1.71.32-.43.16-.74.36-1.06.68-.32.32-.52.63-.68 1.06-.13.33-.28.81-.32 1.71-.06 1.24-.07 1.6-.07 4.74s.01 3.5.07 4.74c.04.9.19 1.38.32 1.71.16.43.36.74.68 1.06.32.32.63.52 1.06.68.33.13.81.28 1.71.32 1.24.06 1.6.07 4.74.07s3.5-.01 4.74-.07c.9-.04 1.38-.19 1.71-.32.43-.16.74-.36 1.06-.68.32-.32.52-.63.68-1.06.13-.33.28-.81.32-1.71.06-1.24.07-1.6.07-4.74s-.01-3.5-.07-4.74c-.04-.9-.19-1.38-.32-1.71a2.85 2.85 0 0 0-.68-1.06 2.85 2.85 0 0 0-1.06-.68c-.33-.13-.81-.28-1.71-.32-1.24-.06-1.6-.07-4.74-.07zm0 3.06a5.16 5.16 0 1 1 0 10.32 5.16 5.16 0 0 1 0-10.32zm0 1.8a3.36 3.36 0 1 0 0 6.72 3.36 3.36 0 0 0 0-6.72zm5.4-2.78a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4z"/></svg>
                                </a>
                                <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="text-gray-400 transition hover:text-terracotta">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.83 14.12c-.25.7-1.45 1.33-2.03 1.42-.52.08-1.17.11-1.88-.12-.43-.14-.99-.32-1.7-.63-2.99-1.3-4.94-4.32-5.09-4.52-.15-.2-1.22-1.62-1.22-3.1 0-1.47.77-2.2 1.05-2.5.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.59.84 2.04.92 2.19.07.15.12.32.02.52-.1.2-.15.32-.3.5-.15.15-.32.39-.45.52-.15.15-.31.31-.13.61.18.3.8 1.32 1.72 2.14 1.18 1.05 2.18 1.38 2.49 1.53.31.15.49.13.67-.08.18-.2.77-.9.98-1.21.2-.31.41-.26.69-.15.28.1 1.77.83 2.07.98.3.15.5.23.58.35.07.13.07.72-.18 1.42z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-14 flex flex-col items-center gap-3 border-t border-white/10 pt-8 text-xs text-gray-500 sm:flex-row sm:justify-between">
                    <p>&copy; {{ date('Y') }} RLS. Seluruh hak cipta dilindungi.</p>
                    <p class="font-serif italic">Diseduh dengan <span class="text-terracotta">cinta</span>, disajikan dengan hangat.</p>
                </div>
            </div>
        </footer>

        {{-- ============ RESERVASI MEJA (Modal Alpine, 3 langkah) ============ --}}
        <div
            x-data="reservationForm()"
            @open-reservation.window="open = true"
            @keydown.escape.window="open = false"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[95] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Pesan Meja"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" x-transition.opacity.duration.300 @click="open = false"></div>

            <div
                class="relative z-10 w-full max-w-lg overflow-hidden rounded-3xl bg-cream shadow-2xl"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            >
                {{-- Header --}}
                <div class="relative bg-terracotta px-6 py-6 text-white">
                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.226-.737L5 21l1.327-3.103A7.935 7.935 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Reservasi Meja
                    </p>
                    <h3 class="mt-1.5 font-serif text-2xl font-semibold">Pesan meja untuk momenmu</h3>
                    <button type="button" @click="open = false" class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-full bg-white/15 text-white transition hover:bg-white/25" aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Step indicator --}}
                <div class="flex items-center justify-center gap-2 px-6 pt-6">
                    <template x-for="(label, idx) in ['Detail', 'Pilih Meja', 'Konfirmasi']" :key="idx">
                        <div class="flex items-center gap-2">
                            <div x-show="idx > 0" class="h-0.5 w-7 rounded-full" :class="step > idx ? 'bg-terracotta' : 'bg-gray-200'"></div>
                            <div class="flex flex-col items-center gap-1.5">
                                <span
                                    class="grid h-8 w-8 place-items-center rounded-full text-xs font-bold transition-colors duration-300"
                                    :class="step > idx + 1 ? 'bg-sage text-white' : (step === idx + 1 ? 'bg-terracotta text-white' : 'bg-gray-200 text-gray-500')"
                                    x-text="idx + 1"
                                ></span>
                                <span class="hidden text-[10px] font-medium sm:block" :class="step === idx + 1 ? 'font-semibold text-terracotta' : 'text-gray-400'" x-text="label"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Body --}}
                <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-6">

                    {{-- LANGKAH 1: tanggal, jam, jumlah tamu --}}
                    <div x-show="step === 1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="res-date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</label>
                                <input id="res-date" type="date" x-model="date" :min="today()" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-sm text-gray-800 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                            </div>
                            <div>
                                <label for="res-time" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Waktu</label>
                                <select id="res-time" x-model="time" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-sm text-gray-800 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                                    <option value="">Pilih jam</option>
                                    <template x-for="slot in timeSlots()" :key="slot">
                                        <option :value="slot" x-text="slot"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Orang</p>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="n in [1, 2, 3, 4, 5, 6, 8, 10]" :key="n">
                                    <button type="button"
                                            @click="guests = n"
                                            class="rounded-xl border py-2.5 text-sm font-semibold transition"
                                            :class="guests === n ? 'border-terracotta bg-terracotta text-white shadow-md' : 'border-gray-200 bg-white text-gray-800 hover:border-terracotta'"
                                            x-text="n + ' orang'"></button>
                                </template>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-500">Setiap slot reservasi berlaku selama 2 jam.</p>
                        <p x-show="error" x-cloak class="mt-3 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600" x-text="error"></p>

                        <button type="button" @click="checkAvailability()" :disabled="loading"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-terracotta px-6 py-4 text-sm font-semibold text-white shadow-lg transition hover:bg-terracotta/90 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg x-show="loading" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Cek Ketersediaan Meja</span>
                            <span x-show="loading">Memeriksa…</span>
                        </button>
                    </div>

                    {{-- LANGKAH 2: daftar meja tersedia --}}
                    <div x-show="step === 2">
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-dark px-4 py-3.5 text-white">
                            <p class="text-xs leading-relaxed">
                                <span class="text-white/60">Jadwal: </span>
                                <span class="font-semibold" x-text="date"></span> <span class="font-semibold" x-text="time"></span> ·
                                <span class="font-semibold" x-text="guests + ' orang'"></span>
                            </p>
                            <button type="button" @click="step = 1" class="shrink-0 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-terracotta transition hover:bg-white/20">Ubah</button>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <template x-for="t in available" :key="t.id">
                                <button type="button" @click="chooseTable(t)"
                                        class="flex w-full items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white px-4 py-3.5 text-left shadow-sm transition hover:border-terracotta hover:shadow-md">
                                    <span class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sage/20 text-sage">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21V9m0 0V6.5a1.5 1.5 0 011.5-1.5h3A1.5 1.5 0 0115 6.5V9m0 0v12m-9 0h18"/></svg>
                                        </span>
                                        <span>
                                            <span class="block text-sm font-semibold text-gray-900" x-text="t.name"></span>
                                            <span class="block text-xs text-gray-500" x-text="'Kapasitas ' + t.capacity + ' orang'"></span>
                                        </span>
                                    </span>
                                    <span class="rounded-full bg-terracotta/10 px-3 py-1 text-xs font-semibold text-terracotta transition group-hover:bg-terracotta">Pilih</span>
                                </button>
                            </template>

                            <p x-show="available.length === 0" x-cloak class="rounded-2xl border border-terracotta/20 bg-terracotta/5 px-4 py-6 text-center text-sm text-gray-600">
                                <span class="block font-semibold text-gray-800">Maaf, meja penuh untuk jadwal tersebut</span>
                                <span class="mt-0.5 block text-xs" x-text="notice"></span>
                                <button type="button" @click="step = 1" class="mt-3 inline-flex rounded-full bg-terracotta px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-terracotta/90">Ubah Pencarian</button>
                            </p>
                            <p x-show="error" x-cloak class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600" x-text="error"></p>
                        </div>
                    </div>

                    {{-- LANGKAH 3: data diri pelanggan + booking --}}
                    <div x-show="step === 3">
                        <div class="rounded-2xl bg-dark px-4 py-3.5 text-white">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm">
                                    <span class="font-semibold" x-text="selected?.name"></span>
                                    <span class="block text-xs text-white/70" x-text="'Kapasitas ' + selected?.capacity + ' orang'"></span>
                                </p>
                                <button type="button" @click="step = 2" class="shrink-0 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-terracotta transition hover:bg-white/20">Ganti</button>
                            </div>
                            <p class="mt-2 border-t border-white/10 pt-2 text-xs text-white/70">
                                <span x-text="date"></span> · <span x-text="time"></span> · <span x-text="guests + ' orang'"></span>
                            </p>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div>
                                <label for="res-name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Lengkap</label>
                                <input id="res-name" type="text" x-model="name" placeholder="Contoh: Salsabila Putri" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                            </div>
                            <div>
                                <label for="res-phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor HP</label>
                                <input id="res-phone" type="tel" x-model="phone" placeholder="Contoh: 0812-3456-7890" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                            </div>
                            <div>
                                <label for="res-notes" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Catatan Tambahan (opsional)</label>
                                <textarea id="res-notes" x-model="notes" rows="2" placeholder="Contoh: meja dekat jendela, ada anak kecil…" class="w-full resize-none rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20"></textarea>
                            </div>
                        </div>

                        <p x-show="error" x-cloak class="mt-3 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600" x-text="error"></p>

                        <button type="button" @click="submitBooking()" :disabled="loading"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-terracotta px-6 py-4 text-sm font-semibold text-white shadow-lg transition hover:bg-terracotta/90 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg x-show="loading" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Booking Sekarang</span>
                            <span x-show="loading">Mengirim…</span>
                        </button>
                    </div>

                    {{-- LANGKAH 4: sukses --}}
                    <div x-show="step === 4" x-cloak class="py-4 text-center">
                        <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-sage/20 text-sage">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h4 class="mt-4 font-serif text-xl font-semibold text-gray-900">Permintaan berhasil dikirim!</h4>
                        <p class="mt-1 text-sm text-gray-600" x-text="notice"></p>

                        <div class="mx-auto mt-5 max-w-xs rounded-2xl border border-gray-100 bg-white p-4 text-left text-sm shadow-sm">
                            <div class="flex justify-between py-1"><span class="text-gray-500">Meja</span><span class="font-semibold text-gray-900" x-text="success?.table"></span></div>
                            <div class="flex justify-between py-1"><span class="text-gray-500">Tanggal</span><span class="font-semibold text-gray-900" x-text="success?.date"></span></div>
                            <div class="flex justify-between py-1"><span class="text-gray-500">Jam</span><span class="font-semibold text-gray-900" x-text="success?.time"></span></div>
                            <div class="flex justify-between py-1"><span class="text-gray-500">Jumlah</span><span class="font-semibold text-gray-900" x-text="success?.guests + ' orang'"></span></div>
                            <div class="flex justify-between py-1"><span class="text-gray-500">Status</span><span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700" x-text="success?.status"></span></div>
                        </div>

                        <button type="button" @click="open = false; reset()" class="mt-5 inline-flex rounded-full bg-terracotta px-8 py-3 text-sm font-semibold text-white transition hover:bg-terracotta/90">Selesai</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ FLOATING CART BUTTON ============ --}}
        <div class="fixed bottom-6 right-6 z-[70]">
            <button type="button"
                    @click="cartOpen = true"
                    class="group relative grid h-16 w-16 place-items-center rounded-full bg-terracotta text-white shadow-xl ring-4 ring-cream transition hover:bg-terracotta/90 hover:shadow-2xl"
                    aria-label="Buka keranjang">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                <span x-show="totalQty() > 0" x-transition
                      class="absolute -right-1 -top-1 grid h-6 min-w-6 place-items-center rounded-full bg-red-500 px-1.5 text-xs font-bold text-white shadow-md">
                    <span x-text="totalQty()">0</span>
                </span>
            </button>
        </div>

        {{-- Toast saat menu ditambahkan --}}
        <div class="fixed bottom-24 right-6 z-[80]" x-show="toast" x-transition x-cloak>
            <div class="flex items-center gap-3 rounded-2xl border border-sage/20 bg-white px-5 py-4 shadow-xl">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-sage text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </span>
                <p class="text-sm font-medium text-gray-800" x-text="toast">Ditambahkan</p>
            </div>
        </div>

        {{-- ============ OFF-CANVAS CART (Slide-over) ============ --}}
        <div class="fixed inset-0 z-[90]" x-show="cartOpen" x-cloak role="dialog" aria-modal="true" aria-label="Keranjang pesanan">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" x-transition.opacity.duration.300
                 @click="cartOpen = false"></div>

            {{-- Panel sidebar --}}
            <div class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-cream shadow-2xl"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-terracotta text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        </span>
                        <div>
                            <h3 class="font-serif text-xl font-semibold text-gray-900">Pesanan Anda</h3>
                            <p class="text-xs text-gray-500">
                                <span x-text="totalQty()">0</span> item
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="cartOpen = false" class="grid h-9 w-9 place-items-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup keranjang">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Daftar item --}}
                <div class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-200">
                                <img :src="item.image" :alt="item.name" class="h-full w-full object-cover">
                            </div>
                            <div class="flex min-w-0 flex-1 flex-col">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="truncate font-serif text-base font-semibold text-gray-900" x-text="item.name"></h4>
                                    <button type="button"
                                            @click="cart = cart.filter((i) => i.id !== item.id)"
                                            class="shrink-0 text-gray-400 transition hover:text-red-500" aria-label="Hapus item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                                <p class="mt-0.5 text-sm font-semibold text-terracotta" x-text="formatRupiah(item.price)"></p>

                                <div class="mt-auto flex items-center justify-between pt-3">
                                    <div class="flex items-center gap-3 rounded-full border border-gray-100 bg-cream px-2 py-1">
                                        <button type="button"
                                                @click="decreaseQty(item.id)"
                                                class="grid h-7 w-7 place-items-center rounded-full bg-white text-gray-600 shadow-sm transition hover:bg-gray-50" aria-label="Kurangi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                                        </button>
                                        <span class="w-5 text-center text-sm font-semibold text-gray-800" x-text="item.qty"></span>
                                        <button type="button"
                                                @click="increaseQty(item.id)"
                                                class="grid h-7 w-7 place-items-center rounded-full bg-white text-gray-600 shadow-sm transition hover:bg-gray-50" aria-label="Tambah">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="formatRupiah(item.price * item.qty)"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="cart.length === 0" x-transition class="flex flex-col items-center justify-center py-16 text-center">
                        <span class="grid h-16 w-16 place-items-center rounded-full bg-sage/20 text-sage">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        </span>
                        <p class="mt-4 font-serif text-lg font-semibold text-gray-800">Keranjang masih kosong</p>
                        <p class="mt-1 text-sm text-gray-500">Pilih menu favoritmu untuk memulai pesanan.</p>
                        <a href="#menu" @click="cartOpen = false" class="mt-5 rounded-full bg-terracotta px-6 py-3 text-sm font-semibold text-white transition hover:bg-terracotta/90">
                            Lihat Menu
                        </a>
                    </div>
                </div>

                {{-- Footer: total + form --}}
                <div class="border-t border-gray-100 bg-white px-6 py-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Grand Total</span>
                        <span class="font-serif text-2xl font-bold text-gray-900" x-text="formatRupiah(grandTotal())">Rp 0</span>
                    </div>

                    <form method="POST" action="{{ route('checkout.store') }}" class="mt-5 space-y-3">
                        @csrf
                        <input type="hidden" name="cart" :value="cartPayload()">

                        <div>
                            <label for="customer-name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Lengkap</label>
                            <input type="text"
                                   name="customer_name"
                                   id="customer-name"
                                   placeholder="Contoh: Salsabila Putri"
                                   required
                                   class="w-full rounded-xl border border-gray-200 bg-cream px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                        </div>

                        <div>
                            <label for="table-number" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor Meja</label>
                            <input type="text"
                                   name="table_number"
                                   id="table-number"
                                   placeholder="Contoh: Meja 12"
                                   class="w-full rounded-xl border border-gray-200 bg-cream px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20">
                        </div>

                        <div>
                            <label for="order-notes" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Catatan Tambahan</label>
                            <textarea name="notes"
                                      id="order-notes"
                                      rows="2"
                                      placeholder="Contoh: Esnya dikit aja, jangan terlalu manis"
                                      class="w-full resize-none rounded-xl border border-gray-200 bg-cream px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-terracotta focus:outline-none focus:ring-2 focus:ring-terracotta/20"></textarea>
                        </div>

                        <button type="submit"
                                :disabled="cart.length === 0"
                                class="flex w-full items-center justify-center gap-2 rounded-full bg-terracotta px-6 py-4 text-sm font-semibold text-white shadow-lg transition hover:bg-terracotta/90 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50">
                            Lanjut ke Pembayaran
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>

                        <p class="text-center text-xs leading-relaxed text-gray-400">
                            Pembayaran diproses aman melalui Midtrans. Kamu akan diarahkan ke halaman pembayaran setelah menekan tombol di atas.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        {{-- Toggle menu mobile --}}
        <script>
            const toggle = document.getElementById('nav-toggle');
            const menu = document.getElementById('mobile-menu');
            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            }
        </script>

        {{-- Customer Cart (Alpine.js component) --}}
        <script>
            function customerCart() {
                return {
                    cart: [],
                    cartOpen: false,
                    toast: null,
                    toastTimer: null,

                    addToCart(item) {
                        const existing = this.cart.find((i) => i.id === item.id);
                        if (existing) {
                            existing.qty += 1;
                        } else {
                            this.cart.push({ ...item, qty: 1 });
                        }
                        this.showToast(item.name);
                    },

                    increaseQty(id) {
                        const item = this.cart.find((i) => i.id === id);
                        if (item) {
                            item.qty += 1;
                        }
                    },

                    decreaseQty(id) {
                        const item = this.cart.find((i) => i.id === id);
                        if (!item) return;
                        item.qty -= 1;
                        if (item.qty <= 0) {
                            this.cart = this.cart.filter((i) => i.id !== id);
                        }
                    },

                    totalQty() {
                        return this.cart.reduce((sum, i) => sum + i.qty, 0);
                    },

                    grandTotal() {
                        return this.cart.reduce((sum, i) => sum + i.qty * i.price, 0);
                    },

                    formatRupiah(value) {
                        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
                    },

                    cartPayload() {
                        return JSON.stringify(this.cart.map((i) => ({
                            menu_item_id: i.id,
                            name: i.name,
                            price: i.price,
                            quantity: i.qty,
                        })));
                    },

                    showToast(name) {
                        this.toast = name + ' ditambahkan ke keranjang';
                        if (this.toastTimer) clearTimeout(this.toastTimer);
                        this.toastTimer = setTimeout(() => (this.toast = null), 2200);
                    },
                };
            }
        </script>

        {{-- Reservasi Meja (Alpine.js) --}}
        <script>
            function reservationForm() {
                return {
                    open: false,
                    step: 1,
                    loading: false,
                    error: null,
                    notice: '',
                    date: '',
                    time: '',
                    guests: 2,
                    available: [],
                    selected: null,
                    name: '',
                    phone: '',
                    notes: '',
                    success: null,

                    today() {
                        const d = new Date();
                        const p = (n) => String(n).padStart(2, '0');
                        return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
                    },

                    timeSlots() {
                        const slots = [];
                        const p = (n) => String(n).padStart(2, '0');
                        for (let h = 8; h <= 21; h++) {
                            ['00', '30'].forEach((m) => slots.push(`${p(h)}:${p(m)}`));
                        }
                        return slots;
                    },

                    reset() {
                        this.step = 1;
                        this.loading = false;
                        this.error = null;
                        this.notice = '';
                        this.available = [];
                        this.selected = null;
                        this.name = '';
                        this.phone = '';
                        this.notes = '';
                        this.success = null;
                    },

                    async checkAvailability() {
                        this.error = null;
                        if (!this.date || !this.time) {
                            this.error = 'Pilih tanggal dan waktu terlebih dahulu.';
                            return;
                        }
                        this.loading = true;
                        try {
                            const res = await fetch(
                                `/reservations/availability?date=${this.date}&time=${this.time}&guests=${this.guests}`,
                                { headers: { Accept: 'application/json' } }
                            );
                            const data = await res.json();
                            if (!res.ok) {
                                this.error = data.message || 'Gagal memeriksa ketersediaan.';
                                return;
                            }
                            this.available = data.available;
                            this.notice = data.message || '';
                            this.step = 2;
                        } catch (e) {
                            this.error = 'Terjadi kesalahan saat memeriksa ketersediaan.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    chooseTable(table) {
                        this.selected = table;
                        this.step = 3;
                    },

                    async submitBooking() {
                        this.error = null;
                        if (!this.selected) {
                            this.error = 'Pilih meja terlebih dahulu.';
                            return;
                        }
                        if (!this.name.trim() || !this.phone.trim()) {
                            this.error = 'Nama dan Nomor HP wajib diisi.';
                            return;
                        }
                        this.loading = true;
                        try {
                            const res = await fetch('/reservations', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    dining_table_id: this.selected.id,
                                    customer_name: this.name,
                                    customer_phone: this.phone,
                                    reservation_date: this.date,
                                    reservation_time: this.time,
                                    guest_count: this.guests,
                                    special_requests: this.notes,
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.error = data.message || 'Gagal mengirim permintaan reservasi.';
                                return;
                            }
                            this.success = data.reservation;
                            this.notice = data.message;
                            this.step = 4;
                        } catch (e) {
                            this.error = 'Terjadi kesalahan saat mengirim permintaan.';
                        } finally {
                            this.loading = false;
                        }
                    },
                };
            }
        </script>
    </body>
</html>