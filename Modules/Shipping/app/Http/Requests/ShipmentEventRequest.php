<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_id' => 'required|exists:shipments,id',
            'driver_id' => 'nullable|exists:delivery_drivers,id',
            'created_by' => 'nullable|exists:users,id',
            'event_type' => 'required|in:status_update,assignment,pickup,location,delivery_attempt,exception,note',
            'status' => 'nullable|in:pending,packed,ready_for_pickup,out_for_delivery,delivered,failed,returned,cancelled',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'occurred_at' => 'required|date',
        ];
    }
}
