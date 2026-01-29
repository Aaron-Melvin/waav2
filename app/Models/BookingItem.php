<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    /** @use HasFactory<\Database\Factories\BookingItemFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'product_id',
        'event_id',
        'unit_id',
        'item_type',
        'starts_on',
        'ends_on',
        'quantity',
        'unit_price',
        'total',
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
            'starts_on' => 'date',
            'ends_on' => 'date',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Booking, BookingItem>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return BelongsTo<Product, BookingItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Event, BookingItem>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<Unit, BookingItem>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
