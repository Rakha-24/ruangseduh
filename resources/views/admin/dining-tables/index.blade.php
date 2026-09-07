<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Kelola Meja</h2>
                <p class="mt-1 text-sm text-coffee-500">Daftar meja, kapasitas, dan status ketersediaan ruang makan.</p>
            </div>
            <a href="{{ route('admin.dining-tables.create') }}" class="btn-primary text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Meja
            </a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-coffee-900 text-cream-50">
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Meja</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Kapasitas</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Total Booking</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="w-64 px-5 py-3.5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-100">
                    @forelse ($tables as $table)
                        <tr class="bg-cream-50 transition hover:bg-cream-100">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-cozy bg-coffee-100 text-coffee-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    </span>
                                    <span class="font-medium text-coffee-900">{{ $table->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid place-items-center rounded-full bg-cream-200 px-3 py-1 text-xs font-bold text-coffee-800">
                                    {{ $table->capacity }} kursi
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-grid min-w-8 place-items-center rounded-full bg-sage-100 px-2.5 py-1 text-xs font-bold text-sage-600">
                                    {{ $table->reservations_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($table->is_active)
                                    <span class="inline-grid place-items-center rounded-full bg-sage-100 px-3 py-1 text-xs font-bold text-sage-700">Aktif</span>
                                @else
                                    <span class="inline-grid place-items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.dining-tables.edit', $table) }}" class="btn-secondary !px-3.5 !py-2 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.dining-tables.toggle', $table) }}">
                                        @csrf
                                        <button type="submit" class="rounded-cozy bg-cream-200 px-3.5 py-2 text-xs font-semibold text-coffee-700 transition hover:bg-cream-300">
                                            {{ $table->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.dining-tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja ini?')">
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
                                Belum ada meja terdaftar. Tambahkan meja pertama untuk mulai menerima reservasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $tables->links() }}</div>
</x-admin-layout>