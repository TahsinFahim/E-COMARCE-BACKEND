<?php

namespace Modules\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Reviews\Services\ProductReviewService;
use Modules\Catalog\Models\Product;

class ProductReviewController extends Controller
{
    protected ProductReviewService $service;

    public function __construct(ProductReviewService $service) { $this->service = $service; }

    public function index()
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        return view('reviews::product-reviews.index', compact('products'));
    }

    public function dataTable(Request $request) { return $this->service->getReviewDataTable($request); }
    public function store(Request $request) { $result = $this->service->saveReview($request->all()); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
    public function show($id) { return response()->json($this->service->getReviewById($id)); }
    public function update(Request $request, $id) { $data = $request->all(); $data['review_id'] = $id; $result = $this->service->saveReview($data); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
    public function destroy($id) { $result = $this->service->deleteReview($id); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
    public function approve($id) { $result = $this->service->approveReview($id); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
}