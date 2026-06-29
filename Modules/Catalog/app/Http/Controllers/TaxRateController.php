<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\TaxRateService;
use Modules\Catalog\Http\Requests\StoreTaxRateRequest;

class TaxRateController extends Controller
{
    protected TaxRateService $taxRateService;

    public function __construct(TaxRateService $taxRateService)
    {
        $this->taxRateService = $taxRateService;
    }

    public function index(Request $request)
    {
        return view('catalog::tax-rates');
    }

    public function dataTable(Request $request)
    {
        return $this->taxRateService->getTaxRateDataTable($request);
    }

    public function store(StoreTaxRateRequest $request)
    {
        $result = $this->taxRateService->saveTaxRate($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->taxRateService->getTaxRateById($id);
        return response()->json($result);
    }

    public function update(StoreTaxRateRequest $request, $id)
    {
        $data = $request->validated();
        $data['tax_rate_id'] = $id;
        $result = $this->taxRateService->saveTaxRate($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->taxRateService->deleteTaxRate($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}