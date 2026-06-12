<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Services\RefundService;

class RefundController extends Controller
{
    protected RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index()
    {
        return view('order::refunds.index');
    }

    public function dataTable(Request $request)
    {
        return $this->refundService->getRefundDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->refundService->saveRefund($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->refundService->getRefundById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['refund_id'] = $id;
        $result = $this->refundService->saveRefund($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->refundService->deleteRefund($id);
        return response()->json($result);
    }
}