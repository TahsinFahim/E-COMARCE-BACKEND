<?php

namespace Modules\Reviews\Services;

use Illuminate\Http\Request;
use Modules\Reviews\Models\WebhookDelivery;
use Yajra\DataTables\DataTables;

class WebhookDeliveryService
{
    public function getDeliveryDataTable(Request $request)
    {
        $query = WebhookDelivery::query()->with('webhook')->orderByDesc('created_at');
        return DataTables::of($query)
            ->addColumn('webhook_name', fn($d) => $d->webhook ? $d->webhook->name : '-')
            ->editColumn('success', fn($d) => $d->success ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-red-600 font-bold">No</span>')
            ->editColumn('delivered_at', fn($d) => $d->delivered_at ? $d->delivered_at->format('d M Y H:i') : '-')
            ->editColumn('created_at', fn($d) => $d->created_at->format('d M Y H:i'))
            ->addColumn('action', fn($d) => view('components.action-buttons', ['id' => $d->id, 'view' => 'webhookDeliveryView', 'delete' => 'webhookDeliveryDelete'])->render())
            ->rawColumns(['action', 'success'])->make(true);
    }

    public function getDeliveryById(int $id): array
    {
        try { $item = WebhookDelivery::with('webhook')->findOrFail($id); return ['status' => 'success', 'delivery' => $item]; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Delivery not found.']; }
    }

    public function deleteDelivery(int $id): array
    {
        try { WebhookDelivery::findOrFail($id)->delete(); return ['status' => 'success', 'message' => 'Delivery deleted.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }
}