<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSeries extends Model
{
    /** @use HasFactory<\Database\Factories\EventSeriesFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'name',
        'starts_at',
        'ends_at',
        'capacity_total',
        'timezone',
        'recurrence_rule',
        'status',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity_total' => 'integer',
            'recurrence_rule' => 'array',
            'starts_at' => 'string',
            'ends_at' => 'string',
        ];
    }

    /**
     * @return BelongsTo<Partner, EventSeries>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, EventSeries>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany<Event>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
