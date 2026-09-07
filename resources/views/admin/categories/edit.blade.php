<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Edit Kategori</h2>
            <p class="mt-1 text-sm text-coffee-500">Slug diperbarui otomatis mengikuti nama.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card max-w-2xl p-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Nama Kategori</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $category->name) }}"
                placeholder="Contoh: Kopi & Non-Kopi"
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500"
            />
            @error('name')
                <p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Perbarui Kategori</button>
            <a href="{{ route('admin.categories.index') }}" class="rounded-cozy px-5 py-3 text-sm font-medium text-coffee-600 transition hover:bg-cream-200">Batal</a>
        </div>
    </form>
</x-admin-layout>