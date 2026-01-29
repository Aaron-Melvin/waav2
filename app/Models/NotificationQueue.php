<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationQueue extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationQueueFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'notification_template_id',
        'channel',
        'recipient',
        'status',
        'attempts',
        'last_error',
        'model_type',
        'model_id',
        'scheduled_for',
        'payload',
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
            'attempts' => 'integer',
            'scheduled_for' => 'datetime',
            'payload' => 'array',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, NotificationQueue>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<NotificationTemplate, NotificationQueue>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
