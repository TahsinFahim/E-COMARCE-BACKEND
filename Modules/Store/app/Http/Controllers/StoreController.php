<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Services\StoreService;
use Modules\Store\Http\Requests\StoreRequest;

class StoreController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index()
    {
        return view('store::stores.index');
    }

    public function dataTable(Request $request)
    {
        return $this->storeService->getStoreDataTable($request);
    }

    public function store(StoreRequest $request)
    {
        $result = $this->storeService->saveStore($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->storeService->getStoreById($id);
        return response()->json($result);
    }

    public function update(StoreRequest $request, $id)
    {
        $data = $request->validated();
        $data['store_id'] = $id;
        $result = $this->storeService->saveStore($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->storeService->deleteStore($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}