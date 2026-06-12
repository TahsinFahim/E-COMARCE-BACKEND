<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\BrandService;
use Modules\Catalog\Http\Requests\StoreBrandRequest;
use Modules\Catalog\Http\Requests\UpdateBrandRequest;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request)
    {
        return view('catalog::brands');
    }

    public function dataTable(Request $request)
    {
        return $this->brandService->getBrandDataTable($request);
    }

    public function store(StoreBrandRequest $request)
    {
        $result = $this->brandService->saveBrand($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->brandService->getBrandById($id);
        return response()->json($result);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $data = $request->validated();
        $data['brand_id'] = $id;
        $result = $this->brandService->saveBrand($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->brandService->deleteBrand($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}
