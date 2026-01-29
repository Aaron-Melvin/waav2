<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'event_series_id',
        'starts_at',
        'ends_at',
        'capacity_total',
        'capacity_reserved',
        'traffic_light',
        'status',
        'publish_state',
        'weather_alert',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity_total' => 'integer',
            'capacity_reserved' => 'integer',
            'weather_alert' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Partner, Event>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, Event>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<EventSeries, Event>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(EventSeries::class, 'event_series_id');
    }

    /**
     * @return HasMany<EventOverride>
     */
    public function overrides(): HasMany
    {
        return $this->hasMany(EventOverride::class);
    }
}
