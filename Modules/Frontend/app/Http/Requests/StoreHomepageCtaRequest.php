<?php

namespace Modules\Frontend\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomepageCtaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'subtitle'          => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'button_text'       => 'required|string|max:255',
            'button_link'       => 'required|string|max:500',
            'background_color'  => 'nullable|string|max:20',
            'text_color'        => 'nullable|string|max:20',
            'button_color'      => 'nullable|string|max:20',
            'button_text_color' => 'nullable|string|max:20',
            'sort_order'        => 'nullable|integer|min:0',
            'status'            => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'CTA title is required.',
            'button_text.required' => 'Button text is required.',
            'button_link.required' => 'Button link is required.',
            'image.image'          => 'The file must be an image.',
            'image.mimes'          => 'Supported formats: jpeg, png, jpg, gif, svg, webp.',
            'image.max'            => 'Image size must not exceed 2MB.',
            'status.in'            => 'Status must be active or inactive.',
        ];
    }
}