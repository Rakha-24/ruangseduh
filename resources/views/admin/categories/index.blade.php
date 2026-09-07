<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Kelola Kategori</h2>
                <p class="mt-1 text-sm text-coffee-500">Kelompok menu untuk memudahkan pelanggan menjelajah.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn-primary text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-coffee-900 text-cream-50">
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Slug</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Jumlah Menu</th>
                        <th class="w-44 px-5 py-3.5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-100">
                    @forelse ($categories as $category)
                        <tr class="bg-cream-50 transition hover:bg-cream-100">
                            <td class="px-5 py-4 font-medium text-coffee-900">{{ $category->name }}</td>
                            <td class="px-5 py-4 font-mono text-xs text-coffee-400">{{ $category->slug }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid min-w-8 place-items-center rounded-full bg-sage-100 px-2.5 py-1 text-xs font-bold text-sage-600">
                                    {{ $category->menu_items_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-secondary !px-3.5 !py-2 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-cozy bg-terracotta-500 px-3.5 py-2 text-xs font-semibold text-cream-50 transition hover:bg-terracotta-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-coffee-500">
                                Belum ada kategori. Yuk buat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>