<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Services\AddressService;
use Modules\Store\Http\Requests\AddressRequest;
use Modules\Store\Services\CountryService;
use Modules\Store\Services\StoreService;

class AddressController extends Controller
{
    protected AddressService $addressService;
    protected CountryService $countryService;
    protected StoreService $storeService;

    public function __construct(AddressService $addressService, CountryService $countryService, StoreService $storeService)
    {
        $this->addressService = $addressService;
        $this->countryService = $countryService;
        $this->storeService = $storeService;
    }

    public function index()
    {
        $countries = $this->countryService->getAllCountries();
        $stores = $this->storeService->getAllActiveStores();
        return view('store::addresses.index', compact('countries', 'stores'));
    }

    public function dataTable(Request $request)
    {
        return $this->addressService->getAddressDataTable($request);
    }

    public function store(AddressRequest $request)
    {
        $result = $this->addressService->saveAddress($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->addressService->getAddressById($id);
        return response()->json($result);
    }

    public function update(AddressRequest $request, $id)
    {
        $data = $request->validated();
        $data['address_id'] = $id;
        $result = $this->addressService->saveAddress($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->addressService->deleteAddress($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}