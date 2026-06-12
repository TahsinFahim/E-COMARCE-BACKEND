<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Store\Models\Store;

class PurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'store_id',
        'status',
        'total_amount',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'payment_status',
        'expected_delivery_date',
        'received_date',
        'notes',
        'order_date',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\Identity\Models\User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public static function generatePoNumber(): string
    {
        $year = date('Y');
        $lastPo = static::whereYear('created_at', $year)->latest('id')->first();
        $sequence = $lastPo ? intval(substr($lastPo->po_number, -4)) + 1 : 1;
        return 'PO-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}