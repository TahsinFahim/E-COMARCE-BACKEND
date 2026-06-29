<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Http\Resources\BannerResource;
use Modules\Frontend\Models\Banner;

class BannerApiController extends Controller
{
    /**
     * Cache TTL in seconds (default: 1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get all active banners.
     *
     * Results are cached for 1 hour. Cache is invalidated when banners
     * are created, updated, or deleted via the admin panel.
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
        $status   = $request->query('status', 'active');
        $perPage  = $request->has('per_page') ? min((int) $request->query('per_page', 10), 100) : null;
        $refresh  = $request->query('refresh', 0) == 1;

        $cacheKey = 'banners:' . ($status ?? 'all') . ':' . ($perPage ?? 'all');

        // Force refresh if requested
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($status, $perPage) {
            $query = Banner::where('status', $status)
                ->orderBy('sort_order')
                ->orderByDesc('created_at');

            if ($perPage) {
                return $query->paginate($perPage);
            }

            return $query->get();
        });

        $response = [
            'success' => true,
            'message' => 'Banners retrieved successfully.',
            'data'    => BannerResource::collection($data),
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
     * Get a single banner by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $cacheKey = 'banner:' . $id;

        $banner = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($id) {
            return Banner::find($id);
        });

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner retrieved successfully.',
            'data'    => new BannerResource($banner),
        ]);
    }
}