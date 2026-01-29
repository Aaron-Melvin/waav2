<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'booking_id',
        'provider',
        'provider_payment_id',
        'amount',
        'currency',
        'status',
        'captured_at',
        'raw_payload',
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
            'amount' => 'decimal:2',
            'captured_at' => 'datetime',
            'raw_payload' => 'array',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Payment>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Booking, Payment>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return HasMany<Refund>
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
