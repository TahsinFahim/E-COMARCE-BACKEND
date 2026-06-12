<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $driverId = $this->route('delivery_driver') ?? $this->input('driver_id');

        return [
            'user_id' => 'nullable|exists:users,id',
            'store_id' => 'nullable|exists:stores,id',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'employee_code' => ['required', 'string', 'max:80', Rule::unique('delivery_drivers', 'employee_code')->ignore($driverId)],
            'name' => 'required|string|max:160',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:160',
            'license_number' => 'nullable|string|max:120',
            'vehicle_type' => 'required|in:walk,bike,motorbike,car,van,truck',
            'vehicle_plate' => 'nullable|string|max:80',
            'capacity_kg' => 'nullable|numeric|min:0|max:999999',
            'status' => 'required|in:available,busy,offline,inactive',
            'current_latitude' => 'nullable|numeric|between:-90,90',
            'current_longitude' => 'nullable|numeric|between:-180,180',
            'last_seen_at' => 'nullable|date',
        ];
    }
}
