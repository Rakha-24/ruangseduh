<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Kelola Promo</h2>
                <p class="mt-1 text-sm text-coffee-500">Promosi banner yang tampil di halaman depan berperiode waktu.</p>
            </div>
            <a href="{{ route('admin.promo.create') }}" class="btn-primary text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Promo
            </a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-coffee-900 text-cream-50">
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Gambar</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Promo</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Periode</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="w-44 px-5 py-3.5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-100">
                    @forelse ($promotions as $promo)
                        @php
                            $status = $promo->is_active
                                ? ['label' => 'Aktif', 'badge' => 'bg-sage-100 text-sage-600']
                                : ['label' => 'Nonaktif', 'badge' => 'bg-coffee-100 text-coffee-400'];
                        @endphp
                        <tr class="bg-cream-50 transition hover:bg-cream-100">
                            <td class="px-5 py-4">
                                @if ($promo->image_url)
                                    <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="h-16 w-16 rounded object-cover shadow-warm" loading="lazy" />
                                @else
                                    <span class="grid h-16 w-16 place-items-center rounded bg-coffee-100 text-[10px] font-semibold uppercase tracking-wider text-coffee-400">No Foto</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-coffee-900">{{ $promo->title }}</p>
                                <p class="max-w-md truncate text-xs text-coffee-400">{{ $promo->description }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-coffee-600">
                                <p>{{ $promo->start_date->format('d M Y') }}</p>
                                <p class="text-coffee-400">s/d {{ $promo->end_date->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $status['badge'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span> {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.promo.edit', $promo) }}" class="btn-secondary !px-3.5 !py-2 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.promo.destroy', $promo) }}" onsubmit="return confirm('Hapus promo ini?')">
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
                                Belum ada promo. Tambahkan promosi pertama untuk menarik pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $promotions->links() }}</div>
</x-admin-layout>