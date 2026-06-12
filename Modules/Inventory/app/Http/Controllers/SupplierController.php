<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Services\SupplierService;
use Modules\Inventory\Http\Requests\SupplierRequest;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        return view('inventory::suppliers.index');
    }

    public function dataTable(Request $request)
    {
        return $this->supplierService->getSupplierDataTable($request);
    }

    public function store(SupplierRequest $request)
    {
        $result = $this->supplierService->saveSupplier($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->supplierService->getSupplierById($id);
        return response()->json($result);
    }

    public function update(SupplierRequest $request, $id)
    {
        $data = $request->validated();
        $data['supplier_id'] = $id;
        $result = $this->supplierService->saveSupplier($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->supplierService->deleteSupplier($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}