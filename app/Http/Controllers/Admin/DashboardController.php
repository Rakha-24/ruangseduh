<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Ringkasan panel admin.
     */
    public function __invoke(Request $request): View
    {
        $ordersToday = Order::query()->whereDate('created_at', now()->toDateString());

        $stats = [
            'menu_active' => MenuItem::available()->count(),
            'menu_total' => MenuItem::count(),
            'categories' => Category::count(),
            'orders_today' => (clone $ordersToday)->count(),
            'orders_today_revenue' => (clone $ordersToday)->sum('total_price'),
            'orders_pending' => Order::query()
                ->whereIn('payment_status', [Order::STATUS_PENDING])
                ->count(),
            'promotions_active' => Promotion::active()->count(),
            'gallery_photos' => Gallery::count(),
            'reservations_pending' => Reservation::query()
                ->where('status', Reservation::STATUS_PENDING)
                ->whereDate('reservation_date', '>=', now()->toDateString())
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}