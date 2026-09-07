<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Reservation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Durasi satu slot reservasi dalam menit.
     */
    public const SLOT_MINUTES = 120;

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_CONFIRMED => 'Dikonfirmasi',
        self::STATUS_CANCELLED => 'Dibatalkan',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    protected $fillable = [
        'dining_table_id',
        'customer_name',
        'customer_phone',
        'reservation_date',
        'reservation_time',
        'guest_count',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'reservation_date' => 'date',
    ];

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getDateLabelAttribute(): string
    {
        return $this->reservation_date?->format('d M Y') ?? '-';
    }

    public function getTimeLabelAttribute(): string
    {
        return $this->reservation_time
            ? Carbon::parse($this->reservation_time)->format('H:i')
            : '-';
    }

    /**
     * ID meja yang sudah "terblokir" pada tanggal & waktu tertentu,
     * karena memiliki reservasi pending/confirmed yang jamnya beririsan.
     */
    public static function blockedTableIds(string $date, string $time): array
    {
        $start = $time;

        $end = Carbon::parse($time)->addMinutes(self::SLOT_MINUTES);
        $endTime = $end->format('H:i');

        // Slot yang melewati tengah malam: anggap berlangsung hingga 23:59
        // agar perbandingan waktu tidak "melompat" ke hari berikutnya.
        if ($endTime <= $time) {
            $endTime = '23:59';
        }

        return self::query()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            ->whereRaw('reservation_time < CAST(? AS time)', [$endTime])
            ->whereRaw('(reservation_time + CAST(? AS interval)) > CAST(? AS time)', [self::SLOT_MINUTES.' minutes', $start])
            ->pluck('dining_table_id')
            ->unique()
            ->values()
            ->all();
    }

    public static function isTableBlocked(int $tableId, string $date, string $time): bool
    {
        return in_array($tableId, self::blockedTableIds($date, $time), true);
    }
}