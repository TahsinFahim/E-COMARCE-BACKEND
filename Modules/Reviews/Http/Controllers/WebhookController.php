<?php

namespace Modules\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Reviews\Services\WebhookService;

class WebhookController extends Controller
{
    protected WebhookService $service;

    public function __construct(WebhookService $service) { $this->service = $service; }

    public function index() { return view('reviews::webhooks.index'); }
    public function dataTable(Request $request) { return $this->service->getWebhookDataTable($request); }
    public function store(Request $request) { $result = $this->service->saveWebhook($request->all()); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
    public function show($id) { return response()->json($this->service->getWebhookById($id)); }
    public function update(Request $request, $id) { $data = $request->all(); $data['webhook_id'] = $id; $result = $this->service->saveWebhook($data); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
    public function destroy($id) { $result = $this->service->deleteWebhook($id); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
}