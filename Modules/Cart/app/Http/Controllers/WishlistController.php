<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cart\Services\WishlistService;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        return view('cart::wishlists.index');
    }

    public function dataTable(Request $request)
    {
        return $this->wishlistService->getWishlistDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->wishlistService->saveWishlist($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->wishlistService->getWishlistById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['wishlist_id'] = $id;
        $result = $this->wishlistService->saveWishlist($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->wishlistService->deleteWishlist($id);
        return response()->json($result);
    }
}