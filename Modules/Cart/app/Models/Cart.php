<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cart\Models\CartItem;

class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'store_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\Modules\Identity\Models\User::class);
    }

    public function store()
    {
        return $this->belongsTo(\Modules\Store\Models\Store::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }
}