<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\BookingStatusHistoryFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'from_status',
        'to_status',
        'reason',
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
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Booking, BookingStatusHistory>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
