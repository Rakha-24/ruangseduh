<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Galeri</h2>
            <p class="mt-1 text-sm text-coffee-500">Foto suasana resto yang tampil di halaman depan. Upload & hapus — tanpa proses edit.</p>
        </div>
    </x-slot>

    {{-- Upload form --}}
    <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data" class="card mb-6 p-6">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <div>
                <label for="image" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Foto</label>
                <input
                    id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 file:mr-3 file:rounded-cozy file:border-0 file:bg-coffee-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-cream-50 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('image')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="caption" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Keterangan (opsional)</label>
                <input id="caption" name="caption" type="text" value="{{ old('caption') }}" placeholder="Contoh: Area bar & espresso machine"
                    class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                @error('caption')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Upload Foto
                </button>
            </div>
        </div>
    </form>

    {{-- Grid foto --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
        @forelse ($photos as $photo)
            <figure class="card group overflow-hidden">
                <div class="relative">
                    @if ($photo->image_url)
                        <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Foto galeri' }}" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy" />
                    @else
                        <div class="grid aspect-square w-full place-items-center bg-coffee-100 text-xs font-semibold uppercase tracking-wider text-coffee-400">No Foto</div>
                    @endif
                    <form
                        method="POST"
                        action="{{ route('admin.galeri.destroy', $photo) }}"
                        onsubmit="return confirm('Hapus foto ini dari galeri?')"
                        class="absolute right-2 top-2"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-cozy bg-coffee-900/85 p-2 text-cream-50 backdrop-blur transition hover:bg-terracotta-500" title="Hapus foto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @if ($photo->caption)
                    <figcaption class="px-4 py-3 text-sm text-coffee-600">{{ $photo->caption }}</figcaption>
                @endif
            </figure>
        @empty
            <div class="col-span-full card p-12 text-center text-coffee-500">
                Belum ada foto. Unggah foto pertama untuk mempercantik halaman depan.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $photos->links() }}</div>
</x-admin-layout>