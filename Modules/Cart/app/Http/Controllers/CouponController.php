<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cart\Services\CouponService;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        return view('cart::coupons.index');
    }

    public function dataTable(Request $request)
    {
        return $this->couponService->getCouponDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->couponService->saveCoupon($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->couponService->getCouponById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['coupon_id'] = $id;
        $result = $this->couponService->saveCoupon($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->couponService->deleteCoupon($id);
        return response()->json($result);
    }
}