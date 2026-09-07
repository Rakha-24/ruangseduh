<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreReservationRequest;
use App\Models\DiningTable;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicReservationController extends Controller
{
    /**
     * Pengecekan ketersediaan meja untuk tanggal, jam, dan jumlah tamu tertentu.
     * Meja dengan reservasi pending/confirmed yang jamnya beririsan tidak ditampilkan.
     */
    public function availability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $blocked = Reservation::blockedTableIds($data['date'], $data['time']);

        $tables = DiningTable::query()
            ->where('is_active', true)
            ->where('capacity', '>=', $data['guests'])
            ->whereNotIn('id', $blocked)
            ->orderBy('capacity')
            ->orderBy('name')
            ->get(['id', 'name', 'capacity']);

        return response()->json([
            'available' => $tables,
            'slot_minutes' => Reservation::SLOT_MINUTES,
            'message' => $tables->isEmpty()
                ? 'Maaf, belum ada meja yang tersedia untuk jadwal tersebut.'
                : "Ditemukan {$tables->count()} meja yang cocok.",
        ]);
    }

    /**
     * Simpan permintaan reservasi publik. Status awal: pending.
     * Dilakukan pengecekan ulang agar tidak terjadi double-booking.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $table = DiningTable::findOrFail($data['dining_table_id']);

        if (! $table->is_active) {
            return response()->json([
                'message' => 'Maaf, meja tersebut sedang tidak tersedia.',
            ], 422);
        }

        if ($table->capacity < $data['guest_count']) {
            return response()->json([
                'message' => "Meja {$table->name} hanya muat untuk {$table->capacity} orang.",
            ], 422);
        }

        if (Reservation::isTableBlocked($table->id, $data['reservation_date'], $data['reservation_time'])) {
            return response()->json([
                'message' => "Maaf, meja {$table->name} sudah dibooking pada jadwal tersebut. Silakan pilih meja lain.",
            ], 422);
        }

        $reservation = Reservation::create([
            'dining_table_id' => $table->id,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'reservation_date' => $data['reservation_date'],
            'reservation_time' => $data['reservation_time'],
            'guest_count' => $data['guest_count'],
            'special_requests' => $data['special_requests'] ?? null,
            'status' => Reservation::STATUS_PENDING,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Permintaan reservasi berhasil dikirim dan menunggu konfirmasi.',
            'reservation' => [
                'id' => $reservation->id,
                'table' => $table->name,
                'date' => $reservation->date_label,
                'time' => $reservation->time_label,
                'guests' => $reservation->guest_count,
                'status' => $reservation->status_label,
            ],
        ], 201);
    }
}