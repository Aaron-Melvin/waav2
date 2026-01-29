<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarSyncAccount extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarSyncAccountFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'provider',
        'external_id',
        'email',
        'status',
        'access_token',
        'refresh_token',
        'token_expires_at',
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
            'token_expires_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, CalendarSyncAccount>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<CalendarSyncEvent>
     */
    public function events(): HasMany
    {
        return $this->hasMany(CalendarSyncEvent::class);
    }
}
