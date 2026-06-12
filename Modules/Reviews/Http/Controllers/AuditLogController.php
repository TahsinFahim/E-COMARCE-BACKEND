<?php

namespace Modules\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Reviews\Services\AuditLogService;

class AuditLogController extends Controller
{
    protected AuditLogService $service;

    public function __construct(AuditLogService $service) { $this->service = $service; }

    public function index() { return view('reviews::audit-logs.index'); }
    public function dataTable(Request $request) { return $this->service->getAuditLogDataTable($request); }
    public function show($id) { return response()->json($this->service->getAuditLogById($id)); }
    public function destroy($id) { $result = $this->service->deleteAuditLog($id); return response()->json($result, $result['status'] === 'success' ? 200 : 500); }
}