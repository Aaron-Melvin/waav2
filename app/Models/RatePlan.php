<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePlan extends Model
{
    /** @use HasFactory<\Database\Factories\RatePlanFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'cancellation_policy_id',
        'name',
        'code',
        'pricing_model',
        'currency',
        'status',
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
     * @return BelongsTo<Partner, RatePlan>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, RatePlan>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<CancellationPolicy, RatePlan>
     */
    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    /**
     * @return HasMany<RatePlanPrice>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(RatePlanPrice::class);
    }
}
