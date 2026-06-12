<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\Services\PosSaleService;
use Modules\Pos\Services\PosRegisterService;
use Modules\Pos\Services\PosShiftService;
use Modules\Pos\Http\Requests\PosSaleRequest;
use Modules\Identity\Models\User;

class PosSaleController extends Controller
{
    protected PosSaleService $saleService;
    protected PosRegisterService $registerService;
    protected PosShiftService $shiftService;

    public function __construct(
        PosSaleService $saleService, 
        PosRegisterService $registerService,
        PosShiftService $shiftService
    ) {
        $this->saleService = $saleService;
        $this->registerService = $registerService;
        $this->shiftService = $shiftService;
    }

    public function index()
    {
        $registers = $this->registerService->getAllActiveRegisters();
        $users = User::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name'])->map(function ($user) {
            return ['id' => $user->id, 'name' => $user->name];
        });
        return view('pos::sales.index', compact('registers', 'users'));
    }

    public function dataTable(Request $request)
    {
        return $this->saleService->getSaleDataTable($request);
    }

    public function store(PosSaleRequest $request)
    {
        $result = $this->saleService->saveSale($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->saleService->getSaleById($id);
        return response()->json($result);
    }

    public function update(PosSaleRequest $request, $id)
    {
        $data = $request->validated();
        $data['sale_id'] = $id;
        $result = $this->saleService->saveSale($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->saleService->deleteSale($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function voidSale($id)
    {
        $result = $this->saleService->voidSale($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}