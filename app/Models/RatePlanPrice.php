<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatePlanPrice extends Model
{
    /** @use HasFactory<\Database\Factories\RatePlanPriceFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'rate_plan_id',
        'starts_on',
        'ends_on',
        'price',
        'extra_adult',
        'extra_child',
        'restrictions',
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
            'price' => 'decimal:2',
            'extra_adult' => 'decimal:2',
            'extra_child' => 'decimal:2',
            'restrictions' => 'array',
        ];
    }

    /**
     * @return BelongsTo<RatePlan, RatePlanPrice>
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
}
