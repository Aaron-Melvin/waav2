<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsMessage extends Model
{
    /** @use HasFactory<\Database\Factories\SmsMessageFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'sms_provider_id',
        'related_type',
        'related_id',
        'to',
        'from',
        'body',
        'status',
        'provider_message_id',
        'error_message',
        'sent_at',
        'payload',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, SmsMessage>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<SmsProvider, SmsMessage>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(SmsProvider::class, 'sms_provider_id');
    }

    /**
     * @return MorphTo
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
