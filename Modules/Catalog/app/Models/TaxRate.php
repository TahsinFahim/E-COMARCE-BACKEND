<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tax_rates';

    protected $fillable = [
        'name', 'rate', 'type', 'applies_to', 'status', 'is_default', 'description'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_default' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tax_rates');
    }
}