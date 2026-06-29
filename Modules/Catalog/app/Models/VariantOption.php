<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantOption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'variant_options';

    protected $fillable = [
        'product_variant_id',
        'color_name',
        'color_code',
        'image_url',
        'price_adjustment',
        'stock',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:4',
        'stock' => 'integer',
        'sort_order' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}