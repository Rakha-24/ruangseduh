<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
{
    /**
     * Seed meja dasar restoran (kapasitas bervariasi).
     */
    public function run(): void
    {
        $configs = [
            ['name' => 'Meja 1', 'capacity' => 2],
            ['name' => 'Meja 2', 'capacity' => 2],
            ['name' => 'Meja 3', 'capacity' => 4],
            ['name' => 'Meja 4', 'capacity' => 4],
            ['name' => 'Meja 5', 'capacity' => 6],
            ['name' => 'Meja 6', 'capacity' => 6],
            ['name' => 'VIP A', 'capacity' => 8],
            ['name' => 'VIP B', 'capacity' => 10],
        ];

        $this->dedupeByName();

        foreach ($configs as $config) {
            DiningTable::updateOrCreate(
                ['name' => $config['name']],
                ['capacity' => $config['capacity'], 'is_active' => true]
            );
        }
    }

    /**
     * Hapus baris duplikat (seed lama) agar updateOrCreate tetap stabil.
     */
    private function dedupeByName(): void
    {
        $keepIds = DiningTable::query()
            ->get()
            ->groupBy('name')
            ->map(fn ($group) => $group->first()->id);

        DiningTable::query()->whereNotIn('id', $keepIds)->delete();
    }
}