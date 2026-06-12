<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\Services\PosShiftService;
use Modules\Pos\Services\PosRegisterService;
use Modules\Pos\Http\Requests\PosShiftRequest;
use Modules\Identity\Models\User;

class PosShiftController extends Controller
{
    protected PosShiftService $shiftService;
    protected PosRegisterService $registerService;

    public function __construct(PosShiftService $shiftService, PosRegisterService $registerService)
    {
        $this->shiftService = $shiftService;
        $this->registerService = $registerService;
    }

    public function index()
    {
        $registers = $this->registerService->getAllActiveRegisters();
        $users = User::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name'])->map(function ($user) {
            return ['id' => $user->id, 'name' => $user->name];
        });
        return view('pos::shifts.index', compact('registers', 'users'));
    }

    public function dataTable(Request $request)
    {
        return $this->shiftService->getShiftDataTable($request);
    }

    public function store(PosShiftRequest $request)
    {
        $result = $this->shiftService->saveShift($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->shiftService->getShiftById($id);
        return response()->json($result);
    }

    public function update(PosShiftRequest $request, $id)
    {
        $data = $request->validated();
        $data['shift_id'] = $id;
        $result = $this->shiftService->saveShift($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->shiftService->deleteShift($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function closeShift(Request $request, $id)
    {
        $result = $this->shiftService->closeShift($id, $request->only(['declared_cash', 'notes']));
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}