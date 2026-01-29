<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationTemplateFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'channel',
        'locale',
        'subject',
        'body',
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
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, NotificationTemplate>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<NotificationEvent>
     */
    public function events(): HasMany
    {
        return $this->hasMany(NotificationEvent::class);
    }

    /**
     * @return HasMany<NotificationQueue>
     */
    public function queueItems(): HasMany
    {
        return $this->hasMany(NotificationQueue::class);
    }
}
