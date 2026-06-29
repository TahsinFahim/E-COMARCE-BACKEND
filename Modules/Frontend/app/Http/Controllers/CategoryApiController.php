<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\Models\Category;
use Modules\Frontend\Http\Resources\CategoryResource;
use Modules\Frontend\Http\Resources\HomeProductResource;

class CategoryApiController extends Controller
{
    /**
     * Cache TTL in seconds (default: 1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get all categories (flat list).
     *
     * Returns both parent and child categories together in one flat array.
     * The frontend can use the parent_id field to build the hierarchy as needed.
     *
     * Results are cached for 1 hour.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @queryParam status string Filter by status (active/inactive). Default: active
     * @queryParam per_page int Items per page for pagination. Default: all
     * @queryParam refresh bool Force refresh cache by passing 1. Default: 0
     */
    public function index(Request $request): JsonResponse
    {
        $status  = $request->query('status', 'active');
        $perPage = $request->has('per_page') ? min((int) $request->query('per_page', 10), 100) : null;
        $refresh = $request->query('refresh', 0) == 1;

        $cacheKey = 'categories:' . ($status ?? 'all') . ':' . ($perPage ?? 'all');

        // Force refresh if requested
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($status, $perPage) {
            $query = Category::withCount('products')
                ->where('status', $status)
                ->orderBy('sort_order')
                ->orderBy('name');

            if ($perPage) {
                return $query->paginate($perPage);
            }

            return $query->get();
        });

        $response = [
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data'    => CategoryResource::collection($data),
        ];

        if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Get a single category by slug with its products (paginated).
     *
     * Use this for the "Products by Category" page.
     * The frontend receives the category info along with a paginated list of products.
     *
     * @param string $slug
     * @param Request $request
     * @return JsonResponse
     *
     * @urlParam slug string required The category slug (e.g. "electronics")
     * @queryParam per_page int Items per page. Default: 20, Max: 40
     * @queryParam sort string Sort by: "latest", "price_asc", "price_desc", "name". Default: "latest"
     * @queryParam refresh bool Force refresh cache by passing 1. Default: 0
     */
    public function products(string $slug, Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 20), 40);
        $sort    = $request->query('sort', 'latest');
        $refresh = $request->query('refresh', 0) == 1;

        $cacheKey = 'category_products:' . $slug . ':page' . $request->query('page', 1) . ':per' . $perPage . ':sort' . $sort;

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $result = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($slug, $perPage, $sort) {
            // Find category by slug
            $category = Category::where('slug', $slug)
                ->where('status', 'active')
                ->withCount('products')
                ->first();

            if (!$category) {
                return ['category' => null, 'products' => null, 'meta' => null];
            }

            // Get products for this category
            $query = $category->products()
                ->where('status', 'active')
                ->where('visibility', 'public')
                ->with([
                    'images',
                    'variants' => function ($q) {
                        $q->where('status', 'active');
                    },
                ]);

            // Apply sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('sale_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('sale_price', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name');
                    break;
                default: // latest
                    $query->orderBy('published_at', 'desc');
            }

            $products = $query->paginate($perPage);

            // Build category data
            $categoryImage = null;
            if ($category->image) {
                $categoryImage = asset('storage/' . $category->image);
            } elseif ($category->image_url) {
                $categoryImage = $category->image_url;
            }

            return [
                'category' => [
                    'id'             => $category->id,
                    'name'           => $category->name,
                    'slug'           => $category->slug,
                    'description'    => $category->description,
                    'image'          => $categoryImage,
                    'parent_id'      => $category->parent_id,
                    'products_count' => $category->products_count,
                ],
                'products' => HomeProductResource::collection($products),
                'meta'     => [
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                ],
            ];
        });

        if ($result['category'] === null) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Category products retrieved successfully.',
            'data'     => $result['category'],
            'products' => $result['products'],
            'meta'     => $result['meta'],
        ]);
    }
}
