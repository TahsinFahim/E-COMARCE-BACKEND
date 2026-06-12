<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cart\Services\CartService;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        return view('cart::carts.index');
    }

    public function dataTable(Request $request)
    {
        return $this->cartService->getCartDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->cartService->saveCart($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->cartService->getCartById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['cart_id'] = $id;
        $result = $this->cartService->saveCart($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->cartService->deleteCart($id);
        return response()->json($result);
    }
}