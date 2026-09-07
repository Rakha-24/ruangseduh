<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-semibold text-coffee-900">Kalender Reservasi</h2>
                <p class="mt-1 text-sm text-coffee-500">Tampilan harian: meja di kolom kiri, jam operasional di bagian atas.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.reservations.calendar') }}" class="flex items-center gap-2">
                    <input name="date" type="date" value="{{ $date->toDateString() }}" class="rounded-cozy border-coffee-100 bg-white px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500">
                    <button type="submit" class="btn-primary !py-2.5 text-sm">Tampilkan</button>
                </form>
                <a href="{{ route('admin.reservations.calendar') }}" class="rounded-cozy bg-cream-200 px-4 py-2.5 text-sm font-medium text-coffee-700 transition hover:bg-cream-300">Hari Ini</a>
                <a href="{{ route('admin.reservations.index') }}" class="rounded-cozy bg-cream-200 px-4 py-2.5 text-sm font-medium text-coffee-700 transition hover:bg-cream-300">Daftar</a>
            </div>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-coffee-100 bg-cream-100/60 px-5 py-3.5">
            <div>
                <p class="font-serif text-lg font-semibold text-coffee-900">{{ $date->translatedFormat('l, d F Y') }}</p>
                <p class="text-xs text-coffee-500">Jam operasional {{ $open }} – {{ $close }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-coffee-600">
                <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-sm bg-amber-200 ring-1 ring-amber-400"></span> Menunggu</span>
                <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-sm bg-terracotta-300/70 ring-1 ring-terracotta-500"></span> Dikonfirmasi</span>
                <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-sm bg-sage-200 ring-1 ring-sage-500"></span> Selesai</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[760px] p-5">
                @php
                    $cols = count($hours);
                    $blockColors = [
                        \App\Models\Reservation::STATUS_PENDING => 'bg-amber-200/80 text-amber-950 ring-amber-400',
                        \App\Models\Reservation::STATUS_CONFIRMED => 'bg-terracotta-300/70 text-coffee-900 ring-terracotta-500',
                        \App\Models\Reservation::STATUS_COMPLETED => 'bg-sage-200/80 text-coffee-900 ring-sage-500',
                    ];
                @endphp

                <div class="space-y-1.5">
                    {{-- Header jam --}}
                    <div class="grid items-end" style="grid-template-columns: 120px 1fr">
                        <div class="pb-2 text-[11px] font-bold uppercase tracking-widest text-coffee-400">Meja</div>
                        <div class="grid" style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr))">
                            @foreach ($hours as $hour)
                                <div class="border-l border-coffee-100 pb-2 text-center text-[11px] font-semibold text-coffee-500">{{ $hour }}</div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Baris per meja --}}
                    @forelse ($tables as $table)
                        <div class="grid items-center rounded-cozy border border-coffee-100 bg-cream-50" style="grid-template-columns: 120px 1fr">
                            <div class="px-3 py-2">
                                <p class="text-sm font-semibold text-coffee-900">{{ $table->name }}</p>
                                <p class="text-[11px] text-coffee-400">{{ $table->capacity }} kursi</p>
                            </div>
                            <div class="relative" style="height: 52px">
                                <div class="absolute inset-0 grid" style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr))">
                                    @foreach ($hours as $index => $hour)
                                        <div class="{{ $loop->last ? '' : 'border-l border-coffee-100' }}"></div>
                                    @endforeach
                                </div>

                                @foreach (($reservations[$table->id] ?? []) as $slot)
                                    @php $res = $slot['reservation']; @endphp
                                    <div
                                        class="absolute top-1 bottom-1 overflow-hidden rounded-md px-2 py-1 text-[11px] leading-tight ring-1 {{ $blockColors[$res->status] ?? 'bg-gray-100 text-coffee-700 ring-gray-300' }}"
                                        style="left: {{ $slot['left'] }}%; width: {{ $slot['width'] }}%"
                                        title="{{ $res->customer_name }} · {{ $res->time_label }} · {{ $res->guest_count }} orang · {{ $res->status_label }}"
                                    >
                                        <p class="truncate font-semibold">{{ $res->time_label }} · {{ $res->customer_name }}</p>
                                        <p class="truncate">{{ $res->guest_count }} orang{{ $res->special_requests ? ' · ada catatan' : '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="px-2 py-8 text-center text-sm text-coffee-500">Belum ada meja terdaftar. Tambahkan meja terlebih dahulu.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>