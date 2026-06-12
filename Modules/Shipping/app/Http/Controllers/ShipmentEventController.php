<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Identity\Models\User;
use Modules\Shipping\Http\Requests\ShipmentEventRequest;
use Modules\Shipping\Models\DeliveryDriver;
use Modules\Shipping\Models\Shipment;
use Modules\Shipping\Services\ShipmentEventService;

class ShipmentEventController extends Controller
{
    public function __construct(protected ShipmentEventService $eventService)
    {
    }

    public function index()
    {
        $shipments = Shipment::orderByDesc('created_at')->limit(200)->get();
        $drivers = DeliveryDriver::orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('email')->get();

        return view('shipping::events.index', compact('shipments', 'drivers', 'users'));
    }

    public function dataTable(Request $request)
    {
        return $this->eventService->getEventDataTable($request);
    }

    public function store(ShipmentEventRequest $request)
    {
        $result = $this->eventService->saveEvent($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show(int $id)
    {
        return response()->json($this->eventService->getEventById($id));
    }

    public function update(ShipmentEventRequest $request, int $id)
    {
        $data = $request->validated();
        $data['event_id'] = $id;
        $result = $this->eventService->saveEvent($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy(int $id)
    {
        $result = $this->eventService->deleteEvent($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}
