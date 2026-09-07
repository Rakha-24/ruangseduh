<?php

namespace Database\Seeders;

use App\Models\RestaurantProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@restocafe.test'],
            [
                'name' => 'Admin Ruang Seduh',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        RestaurantProfile::updateOrCreate(
            ['id' => 1],
            [
                'about_text' => 'Selamat datang di Ruang Seduh. Tempat hangat untuk menikmati hidangan dan kopi terbaik.',
                'address' => 'Jl. Kuliner No. 1',
                'phone' => '0812-3456-7890',
                'whatsapp' => '0812-3456-7890',
                'instagram' => 'ruangseduh.official',
                'email' => 'halo@ruangseduh.id',
                'opening_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '22:00'],
                    'thursday' => ['open' => '08:00', 'close' => '22:00'],
                    'friday' => ['open' => '08:00', 'close' => '23:00'],
                    'saturday' => ['open' => '08:00', 'close' => '23:00'],
                    'sunday' => ['open' => '08:00', 'close' => '22:00'],
                ],
                'map_embed_url' => null,
            ]
        );

        $this->call([
            DiningTableSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
