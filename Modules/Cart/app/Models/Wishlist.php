<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user()
    {
        return $this->belongsTo(\Modules\Identity\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(\Modules\Catalog\Models\Product::class);
    }
}