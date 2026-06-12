<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $zoneId = $this->route('delivery_zone') ?? $this->input('zone_id');

        return [
            'store_id' => 'nullable|exists:stores,id',
            'name' => 'required|string|max:160',
            'code' => ['required', 'string', 'max:60', Rule::unique('delivery_zones', 'code')->ignore($zoneId)],
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'country_id' => 'nullable|exists:countries,id',
            'postal_codes' => 'nullable|string|max:1000',
            'base_fee' => 'required|numeric|min:0|max:999999999',
            'per_km_fee' => 'required|numeric|min:0|max:999999999',
            'free_shipping_min' => 'nullable|numeric|min:0|max:999999999',
            'max_delivery_distance_km' => 'nullable|numeric|min:0|max:999999',
            'estimated_min_days' => 'required|integer|min:0|max:90',
            'estimated_max_days' => 'required|integer|min:0|max:90|gte:estimated_min_days',
            'status' => 'required|in:active,inactive',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('postal_codes') && is_string($this->postal_codes)) {
            $postalCodes = collect(explode(',', $this->postal_codes))
                ->map(fn ($code) => trim($code))
                ->filter()
                ->values()
                ->all();

            $this->merge([
                'postal_codes' => empty($postalCodes) ? null : implode(',', $postalCodes),
            ]);
        }
    }
}
