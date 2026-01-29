<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitCalendar extends Model
{
    /** @use HasFactory<\Database\Factories\UnitCalendarFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'unit_id',
        'date',
        'is_available',
        'min_stay_nights',
        'max_stay_nights',
        'reason',
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
            'is_available' => 'boolean',
            'min_stay_nights' => 'integer',
            'max_stay_nights' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Partner, UnitCalendar>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo<Unit, UnitCalendar>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
