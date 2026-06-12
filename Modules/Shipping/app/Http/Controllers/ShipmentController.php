<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Models\Order;
use Modules\Shipping\Http\Requests\ShipmentRequest;
use Modules\Shipping\Models\DeliveryDriver;
use Modules\Shipping\Models\DeliveryZone;
use Modules\Shipping\Services\ShipmentService;
use Modules\Store\Models\Address;
use Modules\Store\Models\Store;

class ShipmentController extends Controller
{
    public function __construct(protected ShipmentService $shipmentService)
    {
    }

    public function index()
    {
        $orders = Order::orderByDesc('created_at')->limit(200)->get();
        $stores = Store::where('status', 'active')->orderBy('name')->get();
        $zones = DeliveryZone::where('status', 'active')->orderBy('name')->get();
        $drivers = DeliveryDriver::whereIn('status', ['available', 'busy'])->orderBy('name')->get();
        $addresses = Address::orderByDesc('created_at')->limit(200)->get();

        return view('shipping::shipments.index', compact('orders', 'stores', 'zones', 'drivers', 'addresses'));
    }

    public function dataTable(Request $request)
    {
        return $this->shipmentService->getShipmentDataTable($request);
    }

    public function store(ShipmentRequest $request)
    {
        $result = $this->shipmentService->saveShipment($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show(int $id)
    {
        return response()->json($this->shipmentService->getShipmentById($id));
    }

    public function update(ShipmentRequest $request, int $id)
    {
        $data = $request->validated();
        $data['shipment_id'] = $id;
        $result = $this->shipmentService->saveShipment($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy(int $id)
    {
        $result = $this->shipmentService->deleteShipment($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}
