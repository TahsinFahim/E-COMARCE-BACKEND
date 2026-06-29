<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Http\Resources\ProductSearchResource;
use Modules\Frontend\Services\ProductSearchService;

class ProductSearchApiController extends Controller
{
    /**
     * Cache TTL in seconds (10 minutes for search).
     */
    protected int $cacheTtl = 600;

    /**
     * Search products with fuzzy matching.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @queryParam q string required The search query (e.g. "smartphne" or "ipad")
     * @queryParam category_id int optional Filter by category ID
     * @queryParam per_page int Items per page. Default: 10, Max: 40
     * @queryParam refresh bool Force refresh cache. Default: 0
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q'           => 'required|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $query      = $request->query('q', '');
        $categoryId = $request->query('category_id');
        $perPage    = $request->has('per_page') ? min((int) $request->query('per_page', 10), 40) : 10;
        $refresh    = $request->query('refresh', 0) == 1;

        // Normalise cache key — include category_id if present
        $cacheKey = 'product_search:' . md5(strtolower(trim($query))) . ':' . $perPage . ':' . ($categoryId ?? 'all');

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $result = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($query, $perPage, $categoryId) {
            $service = app(ProductSearchService::class);
            $searchResult = $service->search($query, $perPage, $categoryId ? (int) $categoryId : null);

            $products = $searchResult['products'];

            if ($products->isEmpty()) {
                return [
                    'products'   => [],
                    'suggestion' => null,
                ];
            }

            return [
                'products'   => ProductSearchResource::collection($products),
                'suggestion' => $searchResult['suggestion'],
            ];
        });

        $response = [
            'success' => true,
            'message' => empty($result['products']) ? 'No products found.' : 'Products found.',
            'data'    => $result['products'],
            'query'   => $query,
        ];

        if ($categoryId) {
            $response['category_id'] = (int) $categoryId;
        }

        if ($result['suggestion']) {
            $response['suggestion'] = $result['suggestion'];
        }

        return response()->json($response);
    }
}