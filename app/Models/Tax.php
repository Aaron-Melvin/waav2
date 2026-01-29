<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'rate',
        'applies_to',
        'is_inclusive',
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
            'rate' => 'decimal:3',
            'is_inclusive' => 'boolean',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Tax>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
