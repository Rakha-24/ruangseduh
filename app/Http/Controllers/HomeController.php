<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\RestaurantProfile;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function index()
    {
        $profile = RestaurantProfile::first();

        $categories = Category::with('menuItems')->get();

        $promotions = Promotion::active()->get();

        $gallery = Gallery::all();

        // Fallback dummy data (Collection) agar desain langsung terlihat saat DB masih kosong.
        $profile = $profile ?? $this->dummyProfile();
        $categories = $categories->isEmpty() ? $this->dummyMenu() : $categories;
        $promotions = $promotions->isEmpty() ? $this->dummyPromotions() : $promotions;
        $gallery = $gallery->isEmpty() ? $this->dummyGallery() : $gallery;

        return view('welcome', compact('profile', 'categories', 'promotions', 'gallery'));
    }

    /**
     * Dummy profile restoran.
     */
    private function dummyProfile(): RestaurantProfile
    {
        $profile = new RestaurantProfile;

        $profile->about_text = 'Ruang Seduh adalah tempat hangat untuk berkumpul, bekerja, dan menikmati hidangan. Kami menyajikan kopi spesialti dan makanan rumahan yang diracik dengan bahan segar pilihan, dalam suasana yang nyaman dan bersahabat.';
        $profile->address = 'Jl. Kuliner No. 12, Kota Bandung, Jawa Barat 40115';
        $profile->phone = '0812-3456-7890';
        $profile->whatsapp = '6281234567890';
        $profile->instagram = 'ruangseduh.official';
        $profile->email = 'halo@ruangseduh.id';
        $profile->opening_hours = [
            'monday' => ['open' => '08:00', 'close' => '22:00'],
            'tuesday' => ['open' => '08:00', 'close' => '22:00'],
            'wednesday' => ['open' => '08:00', 'close' => '22:00'],
            'thursday' => ['open' => '08:00', 'close' => '22:00'],
            'friday' => ['open' => '08:00', 'close' => '23:00'],
            'saturday' => ['open' => '08:00', 'close' => '23:00'],
            'sunday' => ['open' => '08:00', 'close' => '22:00'],
        ];
        $profile->map_embed_url = null;

        return $profile;
    }

    /**
     * Dummy menu, dikelompokkan per kategori (Collection of Category).
     */
    private function dummyMenu(): Collection
    {
        $coffee = new Category(['name' => 'Kopi & Non-Kopi']);
        $coffee->setRelation('menuItems', collect([
            new MenuItem([
                'name' => 'Espresso',
                'description' => 'Tembakan espresso pekat dari biji arabika pilihan.',
                'price' => 18000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Espresso',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Cappuccino',
                'description' => 'Espresso dengan susu steamed dan busa yang lembut.',
                'price' => 26000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Cappuccino',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Matcha Latte',
                'description' => 'Teh matcha premium dengan susu segar pilihan.',
                'price' => 30000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Matcha+Latte',
                'is_available' => true,
            ]),
        ]));

        $breakfast = new Category(['name' => 'Menu Sarapan']);
        $breakfast->setRelation('menuItems', collect([
            new MenuItem([
                'name' => 'Avocado Toast',
                'description' => 'Roti panggang dengan alpukat, telur, dan taburan biji wijen.',
                'price' => 32000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Avocado+Toast',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Big Breakfast',
                'description' => 'Telur, sosis, daging asap, kentang, dan salad segar.',
                'price' => 45000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Big+Breakfast',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Banana Pancake',
                'description' => 'Pancake lembut dengan irisan pisang dan madu asli.',
                'price' => 35000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Banana+Pancake',
                'is_available' => true,
            ]),
        ]));

        $signature = new Category(['name' => 'Signature Dish']);
        $signature->setRelation('menuItems', collect([
            new MenuItem([
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng dengan ayam suwir, telur mata sapi, dan kerupuk.',
                'price' => 28000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Nasi+Goreng',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Chicken Croissant',
                'description' => 'Croissant buttery dengan isian chicken melt creamy.',
                'price' => 38000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Croissant',
                'is_available' => true,
            ]),
            new MenuItem([
                'name' => 'Beef Steak Bowl',
                'description' => 'Irisan beef tenderloin dengan saus lada hitam dan sayuran.',
                'price' => 68000,
                'image' => 'https://placehold.co/600x400/E3EAD9/33261B?text=Beef+Steak+Bowl',
                'is_available' => true,
            ]),
        ]));

        return collect([$coffee, $breakfast, $signature]);
    }

    /**
     * Dummy promotions aktif.
     */
    private function dummyPromotions(): Collection
    {
        return collect([
            new Promotion([
                'title' => 'Happy Hour 16.00 - 18.00',
                'description' => 'Nikmati diskon 20% untuk semua minuman espresso-based setiap hari.',
                'image' => 'https://placehold.co/1200x500/C4622D/FFFFFF?text=Happy+Hour',
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ]),
            new Promotion([
                'title' => 'Paket Rapat & Meetup',
                'description' => 'Gratis 1 liter es teh untuk pemesanan di atas Rp500.000 setiap sore.',
                'image' => 'https://placehold.co/1200x500/AFC29A/33261B?text=Paket+Meetup',
                'start_date' => now()->subWeek(),
                'end_date' => now()->addWeeks(2),
                'is_active' => true,
            ]),
        ]);
    }

    /**
     * Dummy gallery foto suasana cafe.
     */
    private function dummyGallery(): Collection
    {
        return collect([
            new Gallery([
                'image' => 'https://placehold.co/800x1000/F3E8DA/33261B?text=Bar',
                'caption' => 'Area bar & espresso machine',
            ]),
            new Gallery([
                'image' => 'https://placehold.co/800x800/F3E8DA/33261B?text=Sudut+Cozy',
                'caption' => 'Sudut nyaman untuk bersantai',
            ]),
            new Gallery([
                'image' => 'https://placehold.co/800x1000/F3E8DA/33261B?text=Latte+Art',
                'caption' => 'Latte art signature',
            ]),
            new Gallery([
                'image' => 'https://placehold.co/800x800/F3E8DA/33261B?text=Interior',
                'caption' => 'Interior hangat & terang',
            ]),
            new Gallery([
                'image' => 'https://placehold.co/800x1000/F3E8DA/33261B?text=F%26B',
                'caption' => 'Camilan & dessert',
            ]),
            new Gallery([
                'image' => 'https://placehold.co/800x800/F3E8DA/33261B?text=Teras',
                'caption' => 'Area teras hijau',
            ]),
        ]);
    }
}
