<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\Services\PosSellService;
use Modules\Pos\Services\PosRegisterService;
use Modules\Pos\Services\PosShiftService;

class PosSellController extends Controller
{
    protected PosSellService $sellService;
    protected PosRegisterService $registerService;
    protected PosShiftService $shiftService;

    public function __construct(
        PosSellService $sellService,
        PosRegisterService $registerService,
        PosShiftService $shiftService
    ) {
        $this->sellService = $sellService;
        $this->registerService = $registerService;
        $this->shiftService = $shiftService;
    }

    /**
     * Show the POS create sell interface
     */
    public function index()
    {
        $registers = $this->registerService->getAllActiveRegisters();
        $openShifts = $this->shiftService->getOpenShifts();
        
        return view('pos::sells.index', compact('registers', 'openShifts'));
    }

    /**
     * Search customers by phone/name
     */
    public function searchCustomers(Request $request)
    {
        $result = $this->sellService->searchCustomers($request);
        return response()->json($result);
    }

    /**
     * Search products by name/sku
     */
    public function searchProducts(Request $request)
    {
        $result = $this->sellService->searchProducts($request);
        return response()->json($result);
    }

    /**
     * Process and complete the sale
     */
    public function processSale(Request $request)
    {
        $result = $this->sellService->processSale($request);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Get recent sales for the current register
     */
    public function getRecentSales(Request $request)
    {
        $result = $this->sellService->getRecentSales($request);
        return response()->json($result);
    }
}