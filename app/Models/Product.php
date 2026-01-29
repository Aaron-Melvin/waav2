<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'location_id',
        'name',
        'type',
        'slug',
        'description',
        'capacity_total',
        'default_currency',
        'status',
        'visibility',
        'lead_time_minutes',
        'cutoff_minutes',
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
            'capacity_total' => 'integer',
            'lead_time_minutes' => 'integer',
            'cutoff_minutes' => 'integer',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Product>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Location, Product>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return HasMany<ProductMedia>
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class);
    }

    /**
     * @return HasMany<EventSeries>
     */
    public function eventSeries(): HasMany
    {
        return $this->hasMany(EventSeries::class);
    }

    /**
     * @return HasMany<Event>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return HasMany<EventBlackout>
     */
    public function eventBlackouts(): HasMany
    {
        return $this->hasMany(EventBlackout::class);
    }

    /**
     * @return HasMany<Unit>
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return HasMany<RatePlan>
     */
    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    /**
     * @return HasMany<EligibilityRule>
     */
    public function eligibilityRules(): HasMany
    {
        return $this->hasMany(EligibilityRule::class);
    }
}
