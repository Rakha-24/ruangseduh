<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Tambah Meja</h2>
            <p class="mt-1 text-sm text-coffee-500">Daftarkan meja baru beserta jumlah kursinya.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.dining-tables.store') }}" class="card max-w-2xl p-6">
        @csrf

        <div>
            <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Nama Meja</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                placeholder="Contoh: Meja 1 / VIP A / Jendela"
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500"
            />
            @error('name')
                <p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <label for="capacity" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Kapasitas Kursi</label>
            <input
                id="capacity"
                name="capacity"
                type="number"
                min="1"
                max="500"
                value="{{ old('capacity', 2) }}"
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500"
            />
            @error('capacity')
                <p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <label class="flex items-center gap-3 rounded-cozy border border-coffee-100 bg-cream-50 px-4 py-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="h-5 w-5 rounded border-coffee-200 text-terracotta-500 focus:ring-terracotta-500">
                <span>
                    <span class="block text-sm font-semibold text-coffee-800">Meja aktif</span>
                    <span class="block text-xs text-coffee-500">Meja nonaktif tidak akan muncul pada pencarian ketersediaan.</span>
                </span>
            </label>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Simpan Meja</button>
            <a href="{{ route('admin.dining-tables.index') }}" class="rounded-cozy px-5 py-3 text-sm font-medium text-coffee-600 transition hover:bg-cream-200">Batal</a>
        </div>
    </form>
</x-admin-layout>