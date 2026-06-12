<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'sku', 'product_name',
        'variant_name', 'quantity', 'unit_price', 'discount_total',
        'tax_total', 'line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'discount_total' => 'decimal:4',
        'tax_total' => 'decimal:4',
        'line_total' => 'decimal:4',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(\Modules\Catalog\Models\Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(\Modules\Catalog\Models\ProductVariant::class);
    }
}