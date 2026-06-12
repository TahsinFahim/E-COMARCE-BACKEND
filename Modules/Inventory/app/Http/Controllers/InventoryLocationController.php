<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Services\InventoryLocationService;
use Modules\Inventory\Http\Requests\InventoryLocationRequest;
use Modules\Store\Models\Store;

class InventoryLocationController extends Controller
{
    protected InventoryLocationService $locationService;

    public function __construct(InventoryLocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index()
    {
        $stores = Store::where('status', 'active')->orderBy('name')->get();
        return view('inventory::locations.index', compact('stores'));
    }

    public function dataTable(Request $request)
    {
        return $this->locationService->getLocationDataTable($request);
    }

    public function store(InventoryLocationRequest $request)
    {
        $result = $this->locationService->saveLocation($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->locationService->getLocationById($id);
        return response()->json($result);
    }

    public function update(InventoryLocationRequest $request, $id)
    {
        $data = $request->validated();
        $data['location_id'] = $id;
        $result = $this->locationService->saveLocation($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->locationService->deleteLocation($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}