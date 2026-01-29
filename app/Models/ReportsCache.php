<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportsCache extends Model
{
    /** @use HasFactory<\Database\Factories\ReportsCacheFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'reports_cache';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'report_key',
        'payload',
        'expires_at',
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
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Partner, ReportsCache>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
