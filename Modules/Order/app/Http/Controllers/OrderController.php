<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return view('order::orders.index');
    }

    public function dataTable(Request $request)
    {
        return $this->orderService->getOrderDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->orderService->saveOrder($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->orderService->getOrderById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['order_id'] = $id;
        $result = $this->orderService->saveOrder($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->orderService->deleteOrder($id);
        return response()->json($result);
    }
}