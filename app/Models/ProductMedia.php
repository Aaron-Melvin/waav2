<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    /** @use HasFactory<\Database\Factories\ProductMediaFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'url',
        'kind',
        'sort',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Product, ProductMedia>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
