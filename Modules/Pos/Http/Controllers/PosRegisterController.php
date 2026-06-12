<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\Services\PosRegisterService;
use Modules\Pos\Http\Requests\PosRegisterRequest;
use Modules\Store\Models\Store;

class PosRegisterController extends Controller
{
    protected PosRegisterService $registerService;

    public function __construct(PosRegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function index()
    {
        $stores = Store::where('status', 'active')->orderBy('name')->get();
        return view('pos::registers.index', compact('stores'));
    }

    public function dataTable(Request $request)
    {
        return $this->registerService->getRegisterDataTable($request);
    }

    public function store(PosRegisterRequest $request)
    {
        $result = $this->registerService->saveRegister($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->registerService->getRegisterById($id);
        return response()->json($result);
    }

    public function update(PosRegisterRequest $request, $id)
    {
        $data = $request->validated();
        $data['register_id'] = $id;
        $result = $this->registerService->saveRegister($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->registerService->deleteRegister($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}