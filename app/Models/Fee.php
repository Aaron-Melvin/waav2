<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    /** @use HasFactory<\Database\Factories\FeeFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'type',
        'amount',
        'applies_to',
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
            'amount' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Fee>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
