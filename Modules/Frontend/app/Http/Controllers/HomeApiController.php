<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\Models\Category;
use Modules\Frontend\Http\Resources\HomeProductResource;
use Modules\Frontend\Models\HomepageCta;

class HomeApiController extends Controller
{
    /**
     * Cache TTL in seconds (default: 1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get products grouped by category, with dynamic CTA sections interleaved.
     *
     * Every 2 category sections, a CTA section is inserted (if available).
     * This provides a single API call for the entire home page content.
     *
     * Results are cached for 1 hour. Cache is invalidated when categories
     * or CTAs are created/updated/deleted via the admin panel.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @queryParam limit_categories int Max categories to show. Default: 10
     * @queryParam limit_products int Max products per category. Default: 8
     * @queryParam refresh bool Force refresh cache by passing 1. Default: 0
     */
    public function productsByCategory(Request $request): JsonResponse
    {
        $limitCategories = min((int) $request->query('limit_categories', 10), 50);
        $limitProducts   = min((int) $request->query('limit_products', 8), 20);
        $refresh         = $request->query('refresh', 0) == 1;

        $cacheKey = 'home_products_by_category:cats' . $limitCategories . ':prods' . $limitProducts;

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($limitCategories, $limitProducts) {
            return $this->buildHomePageData($limitCategories, $limitProducts);
        });

        return response()->json([
            'success' => true,
            'message' => 'Home page data retrieved successfully.',
            'data'    => $data,
        ]);
    }

    /**
     * Build the home page data array by interleaving category sections with CTA sections.
     *
     * Logic:
     * 1. Get active categories that have products, sorted by sort_order and name
     * 2. Get active CTAs sorted by sort_order
     * 3. For each category, eager load the top N products with images and variants
     * 4. Interleave: every 2 category sections, insert a CTA section
     *
     * @param int $limitCategories
     * @param int $limitProducts
     * @return array
     */
    private function buildHomePageData(int $limitCategories, int $limitProducts): array
    {
        // 1. Get active categories with products
        $categories = Category::where('status', 'active')
            ->withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take($limitCategories)
            ->get();

        // 2. Get active CTAs sorted by sort_order
        $ctas = HomepageCta::where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // 3. Eager load products for each category
        $categoryIds = $categories->pluck('id')->toArray();
        $productsByCategory = [];

        if (!empty($categoryIds)) {
            // Get all product IDs grouped by category from pivot table
            $pivotRecords = \Illuminate\Support\Facades\DB::table('product_categories')
                ->whereIn('category_id', $categoryIds)
                ->get();

            $productIdsByCategory = [];
            foreach ($pivotRecords as $record) {
                $productIdsByCategory[$record->category_id][] = $record->product_id;
            }

            // Get all unique product IDs
            $allProductIds = array_unique(array_merge(...array_values($productIdsByCategory)));

            if (!empty($allProductIds)) {
                $allProducts = \Modules\Catalog\Models\Product::whereIn('id', $allProductIds)
                    ->where('status', 'active')
                    ->where('visibility', 'public')
                    ->with([
                        'images',
                        'variants' => function ($q) {
                            $q->where('status', 'active');
                        },
                    ])
                    ->orderBy('published_at', 'desc')
                    ->get()
                    ->keyBy('id');

                // Group products by category
                foreach ($productIdsByCategory as $catId => $pIds) {
                    $productsByCategory[$catId] = collect();
                    foreach ($pIds as $pId) {
                        if (isset($allProducts[$pId])) {
                            $productsByCategory[$catId]->push($allProducts[$pId]);
                        }
                    }
                }
            }
        }

        // 4. Build the interleaved data array
        $sections = [];
        $ctaIndex = 0;

        foreach ($categories as $i => $category) {
            // Add category section
            $categoryProducts = isset($productsByCategory[$category->id])
                ? $productsByCategory[$category->id]->take($limitProducts)
                : collect();

            $categoryImage = null;
            if ($category->image) {
                $categoryImage = asset('storage/' . $category->image);
            } elseif ($category->image_url) {
                $categoryImage = $category->image_url;
            }

            $sections[] = [
                'type'     => 'category_section',
                'category' => [
                    'id'          => $category->id,
                    'name'        => $category->name,
                    'slug'        => $category->slug,
                    'description' => $category->description,
                    'image'       => $categoryImage,
                ],
                'products' => HomeProductResource::collection($categoryProducts),
            ];

            // Insert a CTA section after every 2 category sections (if CTAs are available)
            if (($i + 1) % 2 == 0 && isset($ctas[$ctaIndex])) {
                $cta = $ctas[$ctaIndex];
                $sections[] = [
                    'type'              => 'cta_section',
                    'id'                => $cta->id,
                    'title'             => $cta->title,
                    'subtitle'          => $cta->subtitle,
                    'description'       => $cta->description,
                    'image'             => $cta->image ? asset('storage/' . $cta->image) : null,
                    'button_text'       => $cta->button_text,
                    'button_link'       => $cta->button_link,
                    'background_color'  => $cta->background_color,
                    'text_color'        => $cta->text_color,
                    'button_color'      => $cta->button_color,
                    'button_text_color' => $cta->button_text_color,
                ];
                $ctaIndex++;
            }
        }

        return $sections;
    }
}