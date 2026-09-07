<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Tambah Menu</h2>
            <p class="mt-1 text-sm text-coffee-500">Lengkapi data item dan unggah foto — foto akan tampil di menu website.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-6">
        @csrf

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Nama Menu</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Cappuccino"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('name')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Kategori</label>
                <select id="category_id" name="category_id"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500">
                    <option value="">— Pilih Kategori —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Harga (Rp)</label>
                <input id="price" name="price" type="number" step="100" min="0" value="{{ old('price') }}" placeholder="18000"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('price')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="image" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Foto Menu</label>
                <div x-data="{ preview: null }">
                    <input
                        id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 file:mr-3 file:rounded-cozy file:border-0 file:bg-coffee-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-cream-50 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    <template x-if="preview">
                        <img :src="preview" alt="Pratinjau foto menu" class="mt-3 h-28 w-28 rounded object-cover shadow-warm" />
                    </template>
                </div>
                @error('image')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="description" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Deskripsi</label>
            <textarea id="description" name="description" rows="3" placeholder="Kisahkan bahan & cita rasa menu ini..."
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
        </div>

        <div class="mt-5 flex items-center gap-3">
            <input id="is_available" name="is_available" type="checkbox" value="1" checked
                class="h-5 w-5 rounded border-coffee-100 text-terracotta-500 focus:ring-terracotta-500" />
            <label for="is_available" class="text-sm font-medium text-coffee-800">
                Tersedia ditampilkan & bisa dipesan <span class="text-xs font-normal text-coffee-500">(nonaktifkan jika stok habis)</span>
            </label>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Simpan Menu</button>
            <a href="{{ route('admin.menu.index') }}" class="rounded-cozy px-5 py-3 text-sm font-medium text-coffee-600 transition hover:bg-cream-200">Batal</a>
        </div>
    </form>
</x-admin-layout>