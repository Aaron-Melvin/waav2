<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    /** @use HasFactory<\Database\Factories\WebhookFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'url',
        'events',
        'secret',
        'headers',
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
            'events' => 'array',
            'headers' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Webhook>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<WebhookDelivery>
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
