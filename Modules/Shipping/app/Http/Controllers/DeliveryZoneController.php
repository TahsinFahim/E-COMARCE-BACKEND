<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Shipping\Http\Requests\DeliveryZoneRequest;
use Modules\Shipping\Services\DeliveryZoneService;
use Modules\Store\Models\Country;
use Modules\Store\Models\Store;

class DeliveryZoneController extends Controller
{
    public function __construct(protected DeliveryZoneService $zoneService)
    {
    }

    public function index()
    {
        $stores = Store::where('status', 'active')->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('shipping::zones.index', compact('stores', 'countries'));
    }

    public function dataTable(Request $request)
    {
        return $this->zoneService->getZoneDataTable($request);
    }

    public function store(DeliveryZoneRequest $request)
    {
        $result = $this->zoneService->saveZone($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show(int $id)
    {
        return response()->json($this->zoneService->getZoneById($id));
    }

    public function update(DeliveryZoneRequest $request, int $id)
    {
        $data = $request->validated();
        $data['zone_id'] = $id;
        $result = $this->zoneService->saveZone($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy(int $id)
    {
        $result = $this->zoneService->deleteZone($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}
