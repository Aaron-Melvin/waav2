<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancellationPolicy extends Model
{
    /** @use HasFactory<\Database\Factories\CancellationPolicyFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'rules',
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
            'rules' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, CancellationPolicy>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<RatePlan>
     */
    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }
}
