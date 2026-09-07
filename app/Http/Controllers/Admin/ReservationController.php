<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReservationStatusRequest;
use App\Models\DiningTable;
use App\Models\Reservation;
use App\Models\RestaurantProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = Reservation::query()
            ->with(['diningTable:id,name'])
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('reservation_date', $request->input('date')))
            ->when($request->filled('q'), fn ($q) => $q->where('customer_name', 'ilike', '%'.$request->input('q').'%'))
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Kalender harian: meja di sumbu Y, jam operasional di sumbu X.
     */
    public function calendar(Request $request): View
    {
        $profile = RestaurantProfile::getProfile();
        $open = $profile->opening_hours['monday']['open'] ?? '08:00';
        $close = $profile->opening_hours['monday']['close'] ?? '22:00';

        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        Carbon::setLocale('id');

        $openMinutes = self::toMinutes($open);
        $closeMinutes = self::toMinutes($close);

        $hours = [];
        for ($m = $openMinutes; $m < $closeMinutes; $m += 60) {
            $hours[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
        }

        $reservations = Reservation::query()
            ->whereDate('reservation_date', $date->toDateString())
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED, Reservation::STATUS_COMPLETED])
            ->with('diningTable:id,name')
            ->orderBy('reservation_time')
            ->get()
            ->map(function (Reservation $reservation) use ($openMinutes, $closeMinutes) {
                $start = self::toMinutes($reservation->time_label);
                $end = min($start + Reservation::SLOT_MINUTES, 1440);
                $start = max($start, $openMinutes);
                $end = min($end, $closeMinutes);

                $total = max($closeMinutes - $openMinutes, 1);

                return [
                    'reservation' => $reservation,
                    'table' => $reservation->diningTable,
                    'left' => round((($start - $openMinutes) / $total) * 100, 2),
                    'width' => round((($end - $start) / $total) * 100, 2),
                ];
            })
            ->groupBy('table.id');

        $tables = DiningTable::query()->orderBy('name')->get();

        return view('admin.reservations.calendar', compact('date', 'hours', 'tables', 'reservations', 'open', 'close'));
    }

    /**
     * Ubah status reservasi (confirm / cancel / complete / pending).
     */
    public function updateStatus(ReservationStatusRequest $request, Reservation $reservation): RedirectResponse
    {
        $reservation->update(['status' => $request->input('status')]);

        return redirect()
            ->route('admin.reservations.index')
            ->with('status', 'Status reservasi diperbarui menjadi "'.$reservation->status_label.'" untuk '.$reservation->customer_name.'.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()
            ->route('admin.reservations.index')
            ->with('status', 'Reservasi berhasil dihapus.');
    }

    private static function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time));

        return ($h * 60) + $m;
    }
}