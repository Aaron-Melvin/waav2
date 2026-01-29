<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchIndex extends Model
{
    /** @use HasFactory<\Database\Factories\SearchIndexFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'search_index';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'event_id',
        'unit_id',
        'location_id',
        'starts_on',
        'ends_on',
        'capacity_total',
        'capacity_available',
        'price_min',
        'price_max',
        'currency',
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
            'capacity_total' => 'integer',
            'capacity_available' => 'integer',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, SearchIndex>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, SearchIndex>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Event, SearchIndex>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<Unit, SearchIndex>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return BelongsTo<Location, SearchIndex>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
