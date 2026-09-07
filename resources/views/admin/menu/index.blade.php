<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Kelola Menu</h2>
                <p class="mt-1 text-sm text-coffee-500">Semua item yang tampil di halaman depan website.</p>
            </div>
            <a href="{{ route('admin.menu.create') }}" class="btn-primary text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Menu
            </a>
        </div>
    </x-slot>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.menu.index') }}" class="card mb-4 flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <input
                name="q"
                type="search"
                value="{{ request('q') }}"
                placeholder="Cari nama menu..."
                class="w-full rounded-cozy border-coffee-100 bg-white py-2.5 pl-9 pr-3 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500"
            />
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-coffee-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select name="category_id" class="rounded-cozy border-coffee-100 bg-white px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary !px-5 !py-2.5 text-sm">Filter</button>
        @if (request()->has('q') || request()->has('category_id'))
            <a href="{{ route('admin.menu.index') }}" class="text-sm font-medium text-coffee-500 hover:text-coffee-800">Reset</a>
        @endif
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-coffee-900 text-cream-50">
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Gambar</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Menu</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Harga</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="w-44 px-5 py-3.5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-100">
                    @forelse ($menuItems as $menu)
                        <tr class="bg-cream-50 transition hover:bg-cream-100">
                            <td class="px-5 py-4">
                                @if ($menu->image_url)
                                    <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="h-16 w-16 rounded object-cover shadow-warm" loading="lazy" />
                                @else
                                    <span class="grid h-16 w-16 place-items-center rounded bg-coffee-100 text-[10px] font-semibold uppercase tracking-wider text-coffee-400">No Foto</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-coffee-900">{{ $menu->name }}</p>
                                <p class="text-xs text-coffee-400">{{ $menu->category?->name }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-coffee-800">{{ $menu->formatted_price }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($menu->is_available)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sage-100 px-3 py-1 text-xs font-bold text-sage-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-sage-500"></span> Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-coffee-100 px-3 py-1 text-xs font-bold text-coffee-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-coffee-400"></span> Habis
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.menu.edit', $menu) }}" class="btn-secondary !px-3.5 !py-2 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.menu.destroy', $menu) }}" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-cozy bg-terracotta-500 px-3.5 py-2 text-xs font-semibold text-cream-50 transition hover:bg-terracotta-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-coffee-500">
                                Tidak ada menu yang cocok. Tambahkan menu baru ya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $menuItems->links() }}</div>
</x-admin-layout>