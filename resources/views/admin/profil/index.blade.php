<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-serif text-2xl font-semibold text-coffee-900">Profil Resto</h2>
            <p class="mt-1 text-sm text-coffee-500">Informasi kontak & identitas resto yang tampil di website.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.profil.update') }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <section class="card p-6">
            <h3 class="mb-4 font-serif text-lg font-semibold text-coffee-900">Tentang Resto</h3>
            <label for="about_text" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Tentang / Deskripsi</label>
            <textarea id="about_text" name="about_text" rows="4"
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500">{{ old('about_text', $profile->about_text) }}</textarea>
            @error('about_text')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
        </section>

        <section class="card p-6">
            <h3 class="mb-4 font-serif text-lg font-semibold text-coffee-900">Alamat & Kontak</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="address" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Alamat</label>
                    <input id="address" name="address" type="text" value="{{ old('address', $profile->address) }}" placeholder="Jl. Kuliner No. 12, Kota Bandung"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    @error('address')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Telepon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $profile->phone) }}" placeholder="0812-3456-7890"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    @error('phone')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="whatsapp" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">WhatsApp</label>
                    <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $profile->whatsapp) }}" placeholder="6281234567890 (format internasional)"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    @error('whatsapp')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="instagram" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Instagram</label>
                    <input id="instagram" name="instagram" type="text" value="{{ old('instagram', $profile->instagram) }}" placeholder="ruangseduh.official"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    @error('instagram')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $profile->email) }}" placeholder="halo@ruangseduh.id"
                        class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    @error('email')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card p-6">
            <h3 class="mb-1 font-serif text-lg font-semibold text-coffee-900">Jam Operasional</h3>
            <p class="mb-4 text-xs text-coffee-500">Kosongkan jika resto libur di hari tersebut.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-coffee-900 text-cream-50">
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Hari</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Buka</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Tutup</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-coffee-100">
                        @foreach (['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'] as $day => $label)
                            @php
                                $open = old("opening_hours.$day.open", $profile->opening_hours[$day]['open'] ?? '');
                                $close = old("opening_hours.$day.close", $profile->opening_hours[$day]['close'] ?? '');
                            @endphp
                            <tr class="bg-cream-50 transition hover:bg-cream-100">
                                <td class="px-4 py-2.5 font-medium text-coffee-800">{{ $label }}</td>
                                <td class="px-4 py-2.5">
                                    <input type="time" name="opening_hours[{{ $day }}][open]" value="{{ $open }}"
                                        class="rounded-cozy border-coffee-100 bg-white px-3 py-2 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="time" name="opening_hours[{{ $day }}][close]" value="{{ $close }}"
                                        class="rounded-cozy border-coffee-100 bg-white px-3 py-2 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card p-6">
            <h3 class="mb-4 font-serif text-lg font-semibold text-coffee-900">Peta Lokasi</h3>
            <label for="map_embed_url" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-coffee-600">Embed URL Google Maps</label>
            <input id="map_embed_url" name="map_embed_url" type="url" value="{{ old('map_embed_url', $profile->map_embed_url) }}" placeholder="https://www.google.com/maps/embed?pb=..."
                class="w-full rounded-cozy border-coffee-100 bg-white px-3.5 py-2.5 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500" />
            @error('map_embed_url')<p class="mt-1.5 text-xs font-medium text-terracotta-500">{{ $message }}</p>@enderror
        </section>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Simpan Profil Resto</button>
        </div>
    </form>
</x-admin-layout>