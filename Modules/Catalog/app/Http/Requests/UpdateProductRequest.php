<?php

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'brand_id' => 'nullable|integer|exists:brands,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'name' => 'required|string|max:220',
            'slug' => [
                'required',
                'string',
                'max:240',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'product_type' => 'required|in:physical,digital,service,bundle',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|image|max:5120',
            'status' => 'required|in:draft,active,archived',
            'visibility' => 'required|in:public,hidden,private',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            
            // Variant validation
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.sku' => 'required_with:variants|string|max:100',
            'variants.*.name' => 'required_with:variants|string|max:220',
            'variants.*.sale_price' => 'required_with:variants|numeric|min:0',
            'variants.*.cost_price' => 'nullable|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.barcode' => 'nullable|string|max:100',
            'variants.*.weight_grams' => 'nullable|integer|min:0',
            'variants.*.track_inventory' => 'nullable|boolean',
            'variants.*.allow_backorder' => 'nullable|boolean',
            'variants.*.attributes' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'slug.required' => 'Product slug is required.',
            'slug.unique' => 'Product slug must be unique.',
            'product_type.in' => 'Product type is invalid.',
            'status.in' => 'Product status is invalid.',
            'visibility.in' => 'Product visibility is invalid.',
        ];
    }
}
