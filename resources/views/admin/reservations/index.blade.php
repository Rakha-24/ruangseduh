<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Manajemen Reservasi</h2>
                <p class="mt-1 text-sm text-coffee-500">Permintaan booking meja dari pelanggan dan statusnya.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.reservations.calendar') }}" class="btn-secondary text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    Lihat Kalender
                </a>
                <a href="{{ route('admin.dining-tables.index') }}" class="btn-primary text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Kelola Meja
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Filter --}}
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Status</label>
                <select id="status" name="status" class="rounded-cozy border-coffee-100 bg-white px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500">
                    <option value="all">Semua Status</option>
                    @foreach (\App\Models\Reservation::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Tanggal</label>
                <input id="date" name="date" type="date" value="{{ request('date') }}" class="rounded-cozy border-coffee-100 bg-white px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500">
            </div>
            <div class="min-w-56 flex-1">
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Cari Pelanggan</label>
                <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="Nama pelanggan…" class="w-full rounded-cozy border-coffee-100 bg-white px-3 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary !py-2.5 text-sm">Terapkan</button>
                <a href="{{ route('admin.reservations.index') }}" class="rounded-cozy bg-cream-200 px-4 py-2.5 text-sm font-medium text-coffee-700 transition hover:bg-cream-300">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-coffee-900 text-cream-50">
                        <th class="w-16 px-5 py-3.5 font-semibold uppercase tracking-wider">#</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Pelanggan</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Meja</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Jadwal</th>
                        <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Orang</th>
                        <th class="px-5 py-3.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="w-64 px-5 py-3.5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-100">
                    @forelse ($reservations as $reservation)
                        <tr class="bg-cream-50 transition hover:bg-cream-100">
                            <td class="px-5 py-4 font-mono text-xs text-coffee-400">#{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-coffee-900">{{ $reservation->customer_name }}</p>
                                <p class="text-xs text-coffee-500">{{ $reservation->customer_phone }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-coffee-800">{{ $reservation->diningTable?->name ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <p class="text-coffee-800">{{ $reservation->date_label }}</p>
                                <p class="text-xs text-coffee-500">{{ $reservation->time_label }} WIB · slot 2 jam</p>
                                @if ($reservation->special_requests)
                                    <p class="mt-1 max-w-xs truncate text-xs italic text-coffee-400" title="{{ $reservation->special_requests }}">“{{ $reservation->special_requests }}”</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-grid place-items-center rounded-full bg-cream-200 px-3 py-1 text-xs font-bold text-coffee-800">{{ $reservation->guest_count }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php $badge = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'confirmed' => 'bg-sage-100 text-sage-700',
                                    'cancelled' => 'bg-gray-100 text-gray-500',
                                    'completed' => 'bg-coffee-100 text-coffee-700',
                                ][$reservation->status]; @endphp
                                <span class="inline-grid rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $reservation->status_label }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($reservation->status === \App\Models\Reservation::STATUS_PENDING || $reservation->status === \App\Models\Reservation::STATUS_CONFIRMED)
                                        @php $next = $reservation->status === \App\Models\Reservation::STATUS_PENDING ? 'confirmed' : 'completed'; @endphp
                                        <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $next }}">
                                            <button type="submit" class="rounded-cozy bg-sage-500 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-sage-600">
                                                {{ $next === 'confirmed' ? 'Konfirmasi' : 'Selesaikan' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="rounded-cozy border border-terracotta-400 px-3.5 py-2 text-xs font-semibold text-terracotta-500 transition hover:bg-terracotta-500 hover:text-white">Batalkan</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" onsubmit="return confirm('Hapus reservasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-cozy bg-terracotta-500 px-3.5 py-2 text-xs font-semibold text-cream-50 transition hover:bg-terracotta-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-coffee-500">
                                Belum ada reservasi yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $reservations->links() }}</div>
</x-admin-layout>