<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Services\PaymentService;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('order::payments.index');
    }

    public function dataTable(Request $request)
    {
        return $this->paymentService->getPaymentDataTable($request);
    }

    public function store(Request $request)
    {
        $result = $this->paymentService->savePayment($request->all());
        return response()->json($result);
    }

    public function show(int $id)
    {
        $result = $this->paymentService->getPaymentById($id);
        return response()->json($result);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['payment_id'] = $id;
        $result = $this->paymentService->savePayment($data);
        return response()->json($result);
    }

    public function destroy(int $id)
    {
        $result = $this->paymentService->deletePayment($id);
        return response()->json($result);
    }
}