<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'name',
        'email',
        'phone_e164',
        'marketing_opt_in',
        'notifications_opt_in',
        'password',
        'remember_token',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'marketing_opt_in' => 'boolean',
            'notifications_opt_in' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Partner, Customer>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return HasMany<Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<CustomerAccessToken>
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(CustomerAccessToken::class);
    }
}
