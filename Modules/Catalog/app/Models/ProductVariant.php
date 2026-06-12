<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = ['product_id', 'sku', 'barcode', 'name', 'attributes', 'cost_price', 'sale_price', 'compare_at_price', 'weight_grams', 'length_mm', 'width_mm', 'height_mm', 'track_inventory', 'allow_backorder', 'status'];

    protected $casts = ['attributes' => 'array'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }
}
