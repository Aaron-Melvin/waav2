<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'customer_id',
        'coupon_id',
        'status',
        'channel',
        'currency',
        'total_gross',
        'total_tax',
        'total_fees',
        'payment_status',
        'booking_reference',
        'terms_version',
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
            'total_gross' => 'decimal:2',
            'total_tax' => 'decimal:2',
            'total_fees' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Booking>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Customer, Booking>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Coupon, Booking>
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * @return HasMany<BookingItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    /**
     * @return HasMany<BookingAllocation>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(BookingAllocation::class);
    }

    /**
     * @return HasMany<BookingStatusHistory>
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    /**
     * @return HasMany<UnitBookingLock>
     */
    public function unitLocks(): HasMany
    {
        return $this->hasMany(UnitBookingLock::class);
    }

    /**
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Invoice>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
