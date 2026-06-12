<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\ProductVariantService;
use Modules\Catalog\Http\Requests\StoreProductVariantRequest;
use Modules\Catalog\Http\Requests\UpdateProductVariantRequest;

class ProductVariantController extends Controller
{
    protected ProductVariantService $variantService;

    public function __construct(ProductVariantService $variantService)
    {
        $this->variantService = $variantService;
    }

    public function index(Request $request)
    {
        return view('catalog::variants');
    }

    public function dataTable(Request $request)
    {
        return $this->variantService->getVariantDataTable($request);
    }

    public function store(StoreProductVariantRequest $request)
    {
        $result = $this->variantService->saveVariant($request->validated());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->variantService->getVariantById($id);
        return response()->json($result);
    }

    public function update(UpdateProductVariantRequest $request, $id)
    {
        $data = $request->validated();
        $data['variant_id'] = $id;
        $result = $this->variantService->saveVariant($data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->variantService->deleteVariant($id);
        return response()->json($result);
    }
}
