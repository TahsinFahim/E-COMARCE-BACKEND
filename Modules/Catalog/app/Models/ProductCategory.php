<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_categories';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = ['product_id', 'category_id'];
}
