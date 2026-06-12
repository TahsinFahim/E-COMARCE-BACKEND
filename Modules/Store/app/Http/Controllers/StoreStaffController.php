<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Services\StoreStaffService;
use Modules\Store\Http\Requests\StoreStaffRequest;
use Modules\Store\Services\StoreService;

class StoreStaffController extends Controller
{
    protected StoreStaffService $storeStaffService;
    protected StoreService $storeService;

    public function __construct(StoreStaffService $storeStaffService, StoreService $storeService)
    {
        $this->storeStaffService = $storeStaffService;
        $this->storeService = $storeService;
    }

    public function index()
    {
        $stores = $this->storeService->getAllActiveStores();
        return view('store::store-staff.index', compact('stores'));
    }

    public function dataTable(Request $request)
    {
        return $this->storeStaffService->getStoreStaffDataTable($request);
    }

    public function store(StoreStaffRequest $request)
    {
        $result = $this->storeStaffService->saveStoreStaff($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->storeStaffService->getStoreStaffById($id);
        return response()->json($result);
    }

    public function update(StoreStaffRequest $request, $id)
    {
        $data = $request->validated();
        $data['staff_id'] = $id;
        $result = $this->storeStaffService->saveStoreStaff($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->storeStaffService->deleteStoreStaff($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}