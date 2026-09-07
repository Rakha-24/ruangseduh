<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Data referensi: kategori -> daftar menu contoh.
     */
    private const MENUS = [
        'Kopi' => [
            ['name' => 'Espresso', 'description' => 'Kopi hitam pekat dengan crema khas.', 'price' => 18000],
            ['name' => 'Americano', 'description' => 'Espresso dengan tambahan air panas.', 'price' => 20000],
            ['name' => 'Cappuccino', 'description' => 'Espresso, susu steamed, dan busa lembut.', 'price' => 25000],
            ['name' => 'Cafe Latte', 'description' => 'Susu steamed dengan dua shot espresso.', 'price' => 27000],
            ['name' => 'Mocha', 'description' => 'Perpaduan espresso, cokelat, dan susu.', 'price' => 30000],
        ],
        'Non Kopi' => [
            ['name' => 'Teh Tarik', 'description' => 'Teh dengan susu kental manis yang creamy.', 'price' => 18000],
            ['name' => 'Matcha Latte', 'description' => 'Matcha premium dengan susu segar.', 'price' => 28000],
            ['name' => 'Red Velvet Latte', 'description' => 'Minuman creamy khas red velvet.', 'price' => 30000],
            ['name' => 'Cokelat Panas', 'description' => 'Cokelat hangat yang lembut dan manis.', 'price' => 22000],
            ['name' => 'Jus Alpukat', 'description' => 'Jus alpukat kental dengan susu cokelat.', 'price' => 25000],
        ],
        'Makanan' => [
            ['name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan telur, ayam, dan kerupuk.', 'price' => 28000],
            ['name' => 'Ayam Geprek + Nasi', 'description' => 'Ayam goreng digeprek dengan sambal pedas.', 'price' => 25000],
            ['name' => 'Chicken Wings', 'description' => '6 potong sayap ayam dengan saus signature.', 'price' => 30000],
            ['name' => 'French Fries', 'description' => 'Kentang goreng renyah dengan saus.', 'price' => 20000],
        ],
        'Dessert' => [
            ['name' => 'Banana Split', 'description' => 'Pisang, es krim, dan topping cokelat.', 'price' => 25000],
            ['name' => 'Choco Lava Cake', 'description' => 'Kue cokelat dengan lelehan di tengah.', 'price' => 28000],
            ['name' => 'Tiramisu', 'description' => 'Dessert khas Italia dengan coffee aroma.', 'price' => 32000],
        ],
    ];

    /**
     * Seed kategori dan menu contoh ke dalam database.
     */
    public function run(): void
    {
        foreach (self::MENUS as $categoryName => $items) {
            $category = Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($items as $item) {
                MenuItem::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $item['name']],
                    [
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'is_available' => true,
                        'image' => null,
                    ]
                );
            }
        }
    }
}