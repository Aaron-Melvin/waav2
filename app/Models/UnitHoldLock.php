<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitHoldLock extends Model
{
    /** @use HasFactory<\Database\Factories\UnitHoldLockFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hold_id',
        'unit_id',
        'date',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Hold, UnitHoldLock>
     */
    public function hold(): BelongsTo
    {
        return $this->belongsTo(Hold::class);
    }

    /**
     * @return BelongsTo<Unit, UnitHoldLock>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
