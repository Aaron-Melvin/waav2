<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'code',
        'name',
        'occupancy_adults',
        'occupancy_children',
        'status',
        'housekeeping_required',
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
            'occupancy_adults' => 'integer',
            'occupancy_children' => 'integer',
            'housekeeping_required' => 'boolean',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Unit>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, Unit>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany<UnitCalendar>
     */
    public function calendars(): HasMany
    {
        return $this->hasMany(UnitCalendar::class);
    }

    /**
     * @return HasMany<UnitHoldLock>
     */
    public function holdLocks(): HasMany
    {
        return $this->hasMany(UnitHoldLock::class);
    }
}
