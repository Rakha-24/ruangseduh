<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservations(): HasMany
    {
        return $this->reservations()
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED]);
    }
}