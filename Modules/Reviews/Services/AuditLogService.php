<?php

namespace Modules\Reviews\Services;

use Illuminate\Http\Request;
use Modules\Reviews\Models\AuditLog;
use Yajra\DataTables\DataTables;

class AuditLogService
{
    public function getAuditLogDataTable(Request $request)
    {
        $query = AuditLog::query()->with('user')->orderByDesc('created_at');
        return DataTables::of($query)
            ->addColumn('user_name', fn($l) => $l->user ? $l->user->name : 'System')
            ->editColumn('created_at', fn($l) => $l->created_at->format('d M Y H:i'))
            ->addColumn('action', fn($l) => view('components.action-buttons', ['id' => $l->id, 'view' => 'auditLogView', 'delete' => 'auditLogDelete'])->render())
            ->rawColumns(['action'])->make(true);
    }

    public function getAuditLogById(int $id): array
    {
        try { $item = AuditLog::with('user')->findOrFail($id); return ['status' => 'success', 'log' => $item]; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Log not found.']; }
    }

    public function deleteAuditLog(int $id): array
    {
        try { AuditLog::findOrFail($id)->delete(); return ['status' => 'success', 'message' => 'Log deleted.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }
}