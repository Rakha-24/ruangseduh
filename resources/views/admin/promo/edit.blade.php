<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Edit Promo</h2>
            <p class="mt-1 text-sm text-coffee-500">Ubah detail promo; upload banner baru untuk menggantikan yang lama.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.promo.update', $promo) }}" enctype="multipart/form-data" class="card max-w-3xl p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="title" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Judul Promo</label>
                <input id="title" name="title" type="text" value="{{ old('title', $promo->title) }}" placeholder="Contoh: Happy Hour 16.00 - 18.00"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('title')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="image" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Banner Promo</label>
                <div x-data="{ preview: null }" class="flex items-start gap-3">
                    <div>
                        <input
                            id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 file:mr-3 file:rounded-cozy file:border-0 file:bg-coffee-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-cream-50 focus:border-terracotta-500 focus:ring-terracotta-500" />
                        @error('image')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="shrink-0">
                        @if ($promo->image_url)
                            <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="h-20 w-20 rounded object-cover shadow-warm" />
                        @endif
                        <template x-if="preview">
                            <img :src="preview" alt="Pratinjau banner baru" class="mt-2 h-20 w-20 rounded object-cover shadow-warm" />
                        </template>
                    </div>
                </div>
            </div>

            <div>
                <label for="start_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Tanggal Mulai</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $promo->start_date?->format('Y-m-d')) }}"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('start_date')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="end_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Tanggal Berakhir</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $promo->end_date?->format('Y-m-d')) }}"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('end_date')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-5">
            <label for="description" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Deskripsi</label>
            <textarea id="description" name="description" rows="3" placeholder="Detail penawaran promo..."
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500">{{ old('description', $promo->description) }}</textarea>
            @error('description')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
        </div>

        <div class="mt-5 flex items-center gap-3">
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $promo->is_active))
                class="h-5 w-5 rounded border-coffee-100 text-terracotta-500 focus:ring-terracotta-500" />
            <label for="is_active" class="text-sm font-medium text-coffee-800">Aktifkan promo ini sekarang</label>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">Perbarui Promo</button>
            <a href="{{ route('admin.promo.index') }}" class="rounded-cozy px-5 py-3 text-sm font-medium text-coffee-600 transition hover:bg-cream-200">Batal</a>
        </div>
    </form>
</x-admin-layout>