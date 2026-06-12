<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Services\InventoryMovementService;
use Modules\Inventory\Http\Requests\InventoryMovementRequest;
use Modules\Inventory\Models\InventoryLocation;

class InventoryMovementController extends Controller
{
    protected InventoryMovementService $movementService;

    public function __construct(InventoryMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    public function index()
    {
        $locations = InventoryLocation::where('status', 'active')->orderBy('name')->get();
        return view('inventory::movements.index', compact('locations'));
    }

    public function dataTable(Request $request)
    {
        return $this->movementService->getMovementDataTable($request);
    }

    public function store(InventoryMovementRequest $request)
    {
        $result = $this->movementService->saveMovement($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->movementService->getMovementById($id);
        return response()->json($result);
    }

    public function update(InventoryMovementRequest $request, $id)
    {
        $data = $request->validated();
        $data['movement_id'] = $id;
        $result = $this->movementService->saveMovement($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->movementService->deleteMovement($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}