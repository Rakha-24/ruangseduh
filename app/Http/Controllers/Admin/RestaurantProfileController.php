<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRestaurantProfileRequest;
use App\Models\RestaurantProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RestaurantProfileController extends Controller
{
    /**
     * Form pengaturan profil restoran (edit only — setiap entitas memakai
     * satu baris utama lewat RestaurantProfile::getProfile()).
     */
    public function edit(): View
    {
        $profile = RestaurantProfile::getProfile();

        return view('admin.profil.index', compact('profile'));
    }

    /**
     * Simpan/perbarui profil restoran.
     */
    public function update(UpdateRestaurantProfileRequest $request): RedirectResponse
    {
        $profile = RestaurantProfile::getProfile();

        $data = $request->validated();

        // opening_hours dikirim sebagai array n-dimensi [hari => [open, close]].
        if ($request->has('opening_hours')) {
            $hours = [];

            foreach ($request->input('opening_hours', []) as $day => $range) {
                $range = is_array($range) ? $range : [];

                if (! empty($range['open']) || ! empty($range['close'])) {
                    $hours[$day] = [
                        'open' => $range['open'] ?? null,
                        'close' => $range['close'] ?? null,
                    ];
                }
            }

            $data['opening_hours'] = $hours;
        }

        $profile->update($data);

        return redirect()
            ->route('admin.profil')
            ->with('status', 'Profil restoran berhasil diperbarui.');
    }
}