<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'booking_id',
        'number',
        'currency',
        'total_gross',
        'total_tax',
        'total_fees',
        'status',
        'issued_at',
        'due_at',
        'pdf_url',
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
            'total_gross' => 'decimal:2',
            'total_tax' => 'decimal:2',
            'total_fees' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Partner, Invoice>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Booking, Invoice>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
