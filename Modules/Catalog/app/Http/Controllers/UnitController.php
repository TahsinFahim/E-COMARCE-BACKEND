<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\UnitService;
use Modules\Catalog\Http\Requests\StoreUnitRequest;
use Modules\Catalog\Http\Requests\UpdateUnitRequest;

class UnitController extends Controller
{
    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(Request $request)
    {
        return view('catalog::units');
    }

    public function dataTable(Request $request)
    {
        return $this->unitService->getUnitDataTable($request);
    }

    public function store(StoreUnitRequest $request)
    {
        $result = $this->unitService->saveUnit($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->unitService->getUnitById($id);
        return response()->json($result);
    }

    public function update(UpdateUnitRequest $request, $id)
    {
        $data = $request->validated();
        $data['unit_id'] = $id;
        $result = $this->unitService->saveUnit($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->unitService->deleteUnit($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}