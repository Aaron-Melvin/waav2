<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarSyncEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarSyncEventFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'calendar_sync_account_id',
        'product_id',
        'unit_id',
        'event_id',
        'external_event_id',
        'direction',
        'status',
        'last_synced_at',
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
            'last_synced_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<CalendarSyncAccount, CalendarSyncEvent>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(CalendarSyncAccount::class, 'calendar_sync_account_id');
    }

    /**
     * @return BelongsTo<Product, CalendarSyncEvent>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Unit, CalendarSyncEvent>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return BelongsTo<Event, CalendarSyncEvent>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
