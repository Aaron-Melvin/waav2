<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    /** @use HasFactory<\Database\Factories\WebhookDeliveryFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'status',
        'attempt_count',
        'last_error',
        'response_code',
        'response_body',
        'delivered_at',
        'next_retry_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'delivered_at' => 'datetime',
            'next_retry_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Webhook, WebhookDelivery>
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
