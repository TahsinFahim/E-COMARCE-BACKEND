<?php

namespace Modules\Reviews\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Reviews\Models\Notification;
use Yajra\DataTables\DataTables;

class NotificationService
{
    public function getNotificationDataTable(Request $request)
    {
        $query = Notification::query()->with('user')->orderByDesc('created_at');
        return DataTables::of($query)
            ->editColumn('read_at', fn($n) => $n->read_at ? $n->read_at->format('d M Y H:i') : 'Unread')
            ->editColumn('sent_at', fn($n) => $n->sent_at ? $n->sent_at->format('d M Y H:i') : '-')
            ->addColumn('user_name', fn($n) => $n->user ? $n->user->name : 'All Users')
            ->editColumn('created_at', fn($n) => $n->created_at->format('d M Y H:i'))
            ->addColumn('action', fn($n) => view('components.action-buttons', [
                'id' => $n->id, 'edit' => 'notificationEdit', 'delete' => 'notificationDelete',
            ])->render())
            ->rawColumns(['action'])->make(true);
    }

    public function saveNotification(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $id = $data['notification_id'] ?? null; unset($data['notification_id']);
                if ($id) { $item = Notification::findOrFail($id); $item->update($data); $msg = 'Notification updated.'; }
                else { $data['sent_at'] = $data['sent_at'] ?? now(); $item = Notification::create($data); $msg = 'Notification created.'; }
                return ['status' => 'success', 'message' => $msg, 'notification' => $item->fresh()->load('user')];
            });
        } catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }

    public function getNotificationById(int $id): array
    {
        try { $item = Notification::with('user')->findOrFail($id); return ['status' => 'success', 'notification' => $item]; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Notification not found.']; }
    }

    public function deleteNotification(int $id): array
    {
        try { Notification::findOrFail($id)->delete(); return ['status' => 'success', 'message' => 'Notification deleted.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }

    public function markAsRead(int $id): array
    {
        try { Notification::findOrFail($id)->update(['read_at' => now()]); return ['status' => 'success', 'message' => 'Marked as read.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }
}