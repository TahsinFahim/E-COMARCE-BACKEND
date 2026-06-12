<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Identity\Models\User;
use Modules\Shipping\Http\Requests\DeliveryDriverRequest;
use Modules\Shipping\Models\DeliveryZone;
use Modules\Shipping\Services\DeliveryDriverService;
use Modules\Store\Models\Store;

class DeliveryDriverController extends Controller
{
    public function __construct(protected DeliveryDriverService $driverService)
    {
    }

    public function index()
    {
        $stores = Store::where('status', 'active')->orderBy('name')->get();
        $zones = DeliveryZone::where('status', 'active')->orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('email')->get();

        return view('shipping::drivers.index', compact('stores', 'zones', 'users'));
    }

    public function dataTable(Request $request)
    {
        return $this->driverService->getDriverDataTable($request);
    }

    public function store(DeliveryDriverRequest $request)
    {
        $result = $this->driverService->saveDriver($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show(int $id)
    {
        return response()->json($this->driverService->getDriverById($id));
    }

    public function update(DeliveryDriverRequest $request, int $id)
    {
        $data = $request->validated();
        $data['driver_id'] = $id;
        $result = $this->driverService->saveDriver($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy(int $id)
    {
        $result = $this->driverService->deleteDriver($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}
