<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_redemptions',
        'max_per_customer',
        'starts_on',
        'ends_on',
        'min_total',
        'status',
        'meta',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_total' => 'decimal:2',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Coupon>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
