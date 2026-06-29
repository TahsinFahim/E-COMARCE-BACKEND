<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\SizeService;
use Modules\Catalog\Http\Requests\StoreSizeRequest;

class SizeController extends Controller
{
    protected SizeService $sizeService;

    public function __construct(SizeService $sizeService)
    {
        $this->sizeService = $sizeService;
    }

    public function index(Request $request)
    {
        return view('catalog::sizes');
    }

    public function dataTable(Request $request)
    {
        return $this->sizeService->getSizeDataTable($request);
    }

    public function store(StoreSizeRequest $request)
    {
        $result = $this->sizeService->saveSize($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->sizeService->getSizeById($id);
        return response()->json($result);
    }

    public function update(StoreSizeRequest $request, $id)
    {
        $data = $request->validated();
        $data['size_id'] = $id;
        $result = $this->sizeService->saveSize($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->sizeService->deleteSize($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}