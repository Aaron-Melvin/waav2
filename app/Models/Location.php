<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'address_line_1',
        'address_line_2',
        'city',
        'region',
        'postal_code',
        'country_code',
        'latitude',
        'longitude',
        'timezone',
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
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<Partner, Location>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany<EventBlackout>
     */
    public function eventBlackouts(): HasMany
    {
        return $this->hasMany(EventBlackout::class);
    }
}
