<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationEvent extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationEventFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'notification_template_id',
        'event',
        'channel',
        'recipient',
        'status',
        'payload',
        'meta',
        'sent_at',
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
            'meta' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Partner, NotificationEvent>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<NotificationTemplate, NotificationEvent>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }
}
