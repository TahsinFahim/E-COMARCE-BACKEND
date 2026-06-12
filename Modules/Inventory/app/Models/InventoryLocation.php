<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Store\Models\Store;

class InventoryLocation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inventory_locations';

    protected $fillable = [
        'store_id',
        'name',
        'location_type',
        'status',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function stock()
    {
        return $this->hasMany(InventoryStock::class, 'location_id');
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'location_id');
    }
}