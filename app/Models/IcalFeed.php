<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IcalFeed extends Model
{
    /** @use HasFactory<\Database\Factories\IcalFeedFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'product_id',
        'unit_id',
        'name',
        'feed_token',
        'status',
        'last_synced_at',
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
            'last_synced_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, IcalFeed>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Product, IcalFeed>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Unit, IcalFeed>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
