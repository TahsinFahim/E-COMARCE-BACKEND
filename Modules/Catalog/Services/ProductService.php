<?php

namespace Modules\Catalog\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\Brand;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\ProductImage;
use Yajra\DataTables\DataTables;

class ProductService
{
    public function getProductDataTable(Request $request)
    {
        $query = Product::with(['brand'])
            ->select([
                'products.id',
                'products.brand_id',
                'products.name',
                'products.slug',
                'products.product_type',
                'products.status',
                'products.visibility',
                'products.created_at',
            ])
            ->orderByDesc('products.created_at');

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        return DataTables::of($query)
            ->editColumn('status', function (Product $product) {
                return statusBadge($product->status);
            })
            ->editColumn('visibility', function (Product $product) {
                return ucfirst($product->visibility);
            })
            ->editColumn('created_at', function (Product $product) {
                return $product->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Product $product) {
                return view('components.action-buttons', [
                    'id' => $product->id,
                    'edit' => 'productEdit',
                    'delete' => 'productDelete',
                ])->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function saveProduct(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $productId = $data['product_id'] ?? null;

                if ($productId) {
                    $product = Product::findOrFail($productId);
                    $product->update($data);
                    $message = 'Product updated successfully.';
                } else {
                    $product = Product::create($data);
                    $message = 'Product created successfully.';
                }

                if (isset($data['category_ids']) && is_array($data['category_ids'])) {
                    $product->categories()->sync($data['category_ids']);
                }

                if (isset($data['deleted_image_ids']) && is_array($data['deleted_image_ids'])) {
                    $this->removeProductImages($product, $data['deleted_image_ids']);
                }

                if (isset($data['images']) && is_array($data['images'])) {
                    $this->saveProductImages($product, $data['images']);
                }

                // Set main/featured image
                if (isset($data['main_image_id'])) {
                    $mainImageId = (int) $data['main_image_id'];
                    ProductImage::where('product_id', $product->id)->update(['is_main' => false]);
                    ProductImage::where('id', $mainImageId)->where('product_id', $product->id)->update(['is_main' => true]);
                }

                // Handle explicitly deleted variants from edit form
                if (isset($data['deleted_variant_ids']) && is_array($data['deleted_variant_ids'])) {
                    $product->variants()->whereIn('id', $data['deleted_variant_ids'])->delete();
                }

                if (isset($data['variants']) && is_array($data['variants'])) {
                    $keepVariantIds = [];
                    
                    foreach ($data['variants'] as $variantData) {
                        // Handle default values for checkboxes if missing
                        $variantData['track_inventory'] = $variantData['track_inventory'] ?? false;
                        $variantData['allow_backorder'] = $variantData['allow_backorder'] ?? false;
                        
                        $variant = $product->variants()->updateOrCreate(
                            ['id' => $variantData['id'] ?? null], 
                            $variantData
                        );
                        $keepVariantIds[] = $variant->id;
                    }

                    // Delete variants that were removed from the UI and not explicitly tracked
                    if (empty($data['deleted_variant_ids'])) {
                        $product->variants()->whereNotIn('id', $keepVariantIds)->delete();
                    }
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'product' => $product->fresh()->load(['categories', 'variants', 'images', 'brand']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving product: ' . $e->getMessage(),
                'product' => null,
            ];
        }
    }

    public function getProductById(int $id): array
    {
        try {
            $product = Product::with(['brand', 'categories', 'variants', 'images'])->findOrFail($id);
            return [
                'status' => 'success',
                'product' => $product,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Product not found.',
                'product' => null,
            ];
        }
    }

    public function deleteProduct(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $product = Product::findOrFail($id);
                $product->delete();

                return [
                    'status' => 'success',
                    'message' => 'Product deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting product: ' . $e->getMessage(),
            ];
        }
    }

    public function getBrands(): Collection
    {
        return Brand::where('status', 'active')->orderBy('name')->get();
    }

    public function getCategories(): Collection
    {
        return Category::where('status', 'active')->orderBy('name')->get();
    }

    private function saveProductImages(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $fileName = Str::slug($product->name) . '-' . now()->format('YmdHis') . '-' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $fileName, 'public');

                // First image is automatically set as main
                $isMain = !ProductImage::where('product_id', $product->id)->where('is_main', true)->exists() && $index === 0;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => '/storage/' . $path,
                    'alt_text' => $product->name,
                    'sort_order' => ProductImage::where('product_id', $product->id)->max('sort_order') + 1,
                    'is_main' => $isMain,
                ]);
            }
        }
    }

    private function removeProductImages(Product $product, array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = ProductImage::whereIn('id', $imageIds)->where('product_id', $product->id)->get();

        foreach ($images as $image) {
            $this->deleteImage($image->image_url);
            $image->delete();
        }

        // If the main image was deleted, assign main to the first remaining image
        if (!ProductImage::where('product_id', $product->id)->where('is_main', true)->exists()) {
            $firstImage = ProductImage::where('product_id', $product->id)->orderBy('sort_order')->first();
            if ($firstImage) {
                $firstImage->update(['is_main' => true]);
            }
        }
    }

    private function deleteImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH) ?: $imageUrl;

        if (!str_starts_with($path, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($path, strlen('/storage/')));
    }
}
