<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Frontend\Http\Resources\NavbarItemResource;
use Modules\Frontend\Models\NavbarItem;

class NavbarApiController extends Controller
{
    /**
     * Cache TTL in seconds (default: 1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * Get all active navbar items with their subnavbar items.
     *
     * Results are cached for 1 hour. Cache is invalidated when navbar items
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

        $cacheKey = 'navbar_items:' . ($status ?? 'all') . ':' . ($perPage ?? 'all');

        // Force refresh if requested
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($status, $perPage) {
            $query = NavbarItem::with(['subnavbarItems' => function ($q) use ($status) {
                if ($status) {
                    $q->where('status', $status);
                }
                $q->orderBy('sort_order')->orderBy('name');
            }])
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
            'message' => 'Navbar items retrieved successfully.',
            'data'    => NavbarItemResource::collection($data),
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
     * Get a single navbar item with its subnavbar items.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $cacheKey = 'navbar_item:' . $id;

        $navbarItem = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($id) {
            return NavbarItem::with(['subnavbarItems' => function ($q) {
                $q->where('status', 'active')->orderBy('sort_order')->orderBy('name');
            }])->find($id);
        });

        if (!$navbarItem) {
            return response()->json([
                'success' => false,
                'message' => 'Navbar item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Navbar item retrieved successfully.',
            'data'    => new NavbarItemResource($navbarItem),
        ]);
    }

    /**
     * Get all active subnavbar items for a specific navbar item.
     *
     * @param int $navbarItemId
     * @return JsonResponse
     */
    public function children(int $navbarItemId): JsonResponse
    {
        $cacheKey = 'navbar_item_children:' . $navbarItemId;

        $navbarItem = Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($navbarItemId) {
            return NavbarItem::find($navbarItemId);
        });

        if (!$navbarItem) {
            return response()->json([
                'success' => false,
                'message' => 'Navbar item not found.',
            ], 404);
        }

        $childrenCacheKey = 'subnavbar_items:' . $navbarItemId;

        $subnavbarItems = Cache::remember($childrenCacheKey, now()->addSeconds($this->cacheTtl), function () use ($navbarItemId) {
            return NavbarItem::find($navbarItemId)?->subnavbarItems()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'success' => true,
            'message' => 'Subnavbar items retrieved successfully.',
            'data'    => \Modules\Frontend\Http\Resources\SubnavbarItemResource::collection($subnavbarItems),
        ]);
    }
}
