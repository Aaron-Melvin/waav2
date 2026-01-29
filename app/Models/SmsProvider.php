<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsProvider extends Model
{
    /** @use HasFactory<\Database\Factories\SmsProviderFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'provider',
        'credentials',
        'is_default',
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
            'credentials' => 'array',
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Partner, SmsProvider>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<SmsMessage>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }
}
