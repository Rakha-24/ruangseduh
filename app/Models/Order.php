<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'customer_name',
    'customer_phone',
    'table_number',
    'notes',
    'payment_method',
    'total_price',
    'payment_status',
    'amount_paid',
    'snap_token',
])]
class Order extends Model
{
    use HasUuids;

    /**
     * Payment method identifiers.
     */
    public const METHOD_CASH = 'tunai';

    public const METHOD_ONLINE = 'online';

    /**
     * Tax rate applied to POS orders.
     */
    public const TAX_RATE = 0.10;

    /**
     * Payment statuses that are considered final (no longer mutable).
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    public const PAYMENT_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAILED,
        self::STATUS_EXPIRED,
    ];

    /**
     * The order items belonging to this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The total of the order.
     */
    public function getTotalFormattedAttribute(): string
    {
        return 'Rp '.number_format((float) $this->total_price, 0, ',', '.');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'amount_paid' => 'integer',
        ];
    }
}
