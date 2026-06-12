<?php

namespace Modules\Reviews\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Reviews\Models\Webhook;
use Modules\Reviews\Models\WebhookDelivery;
use Yajra\DataTables\DataTables;

class WebhookService
{
    public function getWebhookDataTable(Request $request)
    {
        $query = Webhook::query()->orderByDesc('created_at');
        return DataTables::of($query)
            ->editColumn('status', fn($w) => ucfirst($w->status))
            ->editColumn('created_at', fn($w) => $w->created_at->format('d M Y H:i'))
            ->addColumn('action', fn($w) => view('components.action-buttons', ['id' => $w->id, 'edit' => 'webhookEdit', 'delete' => 'webhookDelete'])->render())
            ->rawColumns(['action'])->make(true);
    }

    public function saveWebhook(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $id = $data['webhook_id'] ?? null; unset($data['webhook_id']);
                if ($id) { $item = Webhook::findOrFail($id); $item->update($data); $msg = 'Webhook updated.'; }
                else { $item = Webhook::create($data); $msg = 'Webhook created.'; }
                return ['status' => 'success', 'message' => $msg, 'webhook' => $item->fresh()];
            });
        } catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }

    public function getWebhookById(int $id): array
    {
        try { $item = Webhook::with('deliveries')->findOrFail($id); return ['status' => 'success', 'webhook' => $item]; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Webhook not found.']; }
    }

    public function deleteWebhook(int $id): array
    {
        try { Webhook::findOrFail($id)->delete(); return ['status' => 'success', 'message' => 'Webhook deleted.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }
}