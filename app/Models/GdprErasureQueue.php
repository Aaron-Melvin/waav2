<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprErasureQueue extends Model
{
    /** @use HasFactory<\Database\Factories\GdprErasureQueueFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'customer_id',
        'status',
        'requested_at',
        'processed_at',
        'notes',
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
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, GdprErasureQueue>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Customer, GdprErasureQueue>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
