<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    /** @use HasFactory<\Database\Factories\IdempotencyKeyFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'scope_type',
        'scope_id',
        'key',
        'request_hash',
        'response',
        'status',
        'expires_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Partner, IdempotencyKey>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
