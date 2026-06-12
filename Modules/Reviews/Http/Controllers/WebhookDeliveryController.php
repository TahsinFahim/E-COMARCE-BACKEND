<?php

namespace Modules\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Reviews\Services\WebhookDeliveryService;

class WebhookDeliveryController extends Controller
{
    protected WebhookDeliveryService $service;

    public function __construct(WebhookDeliveryService $service) { $this->service = $service; }

    public function index() { return view('reviews::webhook-deliveries.index'); }
    public function dataTable(Request $request) { return $this->service->getDeliveryDataTable($request); }
    public function show($id) { return response()->json($this->service->getDeliveryById($id)); }
    public function destroy($id) { $result = $this->service->deleteDelivery($id); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
}