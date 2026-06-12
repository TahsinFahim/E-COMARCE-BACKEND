<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shipmentId = $this->route('shipment') ?? $this->input('shipment_id');

        return [
            'order_id' => 'nullable|exists:orders,id',
            'store_id' => 'nullable|exists:stores,id',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'driver_id' => 'nullable|exists:delivery_drivers,id',
            'shipping_address_id' => 'nullable|exists:addresses,id',
            'tracking_number' => ['nullable', 'string', 'max:80', Rule::unique('shipments', 'tracking_number')->ignore($shipmentId)],
            'carrier_name' => 'nullable|string|max:120',
            'service_level' => 'required|in:standard,express,same_day,pickup',
            'delivery_type' => 'required|in:home_delivery,store_pickup,third_party',
            'status' => 'required|in:pending,packed,ready_for_pickup,out_for_delivery,delivered,failed,returned,cancelled',
            'shipping_cost' => 'required|numeric|min:0|max:999999999',
            'package_weight_kg' => 'nullable|numeric|min:0|max:999999',
            'package_count' => 'required|integer|min:1|max:999',
            'recipient_name' => 'nullable|string|max:160',
            'recipient_phone' => 'nullable|string|max:40',
            'delivery_instructions' => 'nullable|string|max:1000',
            'scheduled_delivery_date' => 'nullable|date',
            'eta_at' => 'nullable|date',
            'shipped_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:shipped_at',
        ];
    }
}
