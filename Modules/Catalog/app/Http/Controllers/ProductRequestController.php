<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Models\ProductRequest;
use Modules\Catalog\Services\ProductRequestService;

class ProductRequestController extends Controller
{
    protected ProductRequestService $productRequestService;

    public function __construct(ProductRequestService $productRequestService)
    {
        $this->productRequestService = $productRequestService;
    }

    public function index()
    {
        return view('catalog::product-requests.index');
    }

    public function dataTable(Request $request)
    {
        return $this->productRequestService->getProductRequestDataTable($request);
    }

    public function show(int $id)
    {
        $result = $this->productRequestService->getProductRequestById($id);
        return response()->json($result);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,fulfilled',
        ]);

        $result = $this->productRequestService->updateStatus($id, $request->input('status'));
        return response()->json($result);
    }
}