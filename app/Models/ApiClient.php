<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class ApiClient extends Model
{
    /** @use HasFactory<\Database\Factories\ApiClientFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'client_id',
        'client_secret_hash',
        'scopes',
        'status',
        'last_used_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'scopes' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, ApiClient>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function verifySecret(string $secret): bool
    {
        return Hash::check($secret, $this->client_secret_hash);
    }
}
