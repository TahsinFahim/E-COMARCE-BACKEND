<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Services\CountryService;
use Modules\Store\Http\Requests\CountryRequest;

class CountryController extends Controller
{
    protected CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        return view('store::countries.index');
    }

    public function dataTable(Request $request)
    {
        return $this->countryService->getCountryDataTable($request);
    }

    public function store(CountryRequest $request)
    {
        $result = $this->countryService->saveCountry($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->countryService->getCountryById($id);
        return response()->json($result);
    }

    public function update(CountryRequest $request, $id)
    {
        $data = $request->validated();
        $data['country_id'] = $id;
        $result = $this->countryService->saveCountry($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->countryService->deleteCountry($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}