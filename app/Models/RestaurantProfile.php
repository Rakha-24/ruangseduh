<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'about_text',
    'address',
    'phone',
    'whatsapp',
    'instagram',
    'email',
    'opening_hours',
    'map_embed_url',
])]
class RestaurantProfile extends Model
{
    /**
     * The single profile row (singleton).
     */
    public static function getProfile(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
        ];
    }
}
