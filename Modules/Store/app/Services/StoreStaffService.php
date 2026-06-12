<?php

namespace Modules\Store\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Store\Models\StoreStaff;
use Yajra\DataTables\DataTables;

class StoreStaffService
{
    public function getStoreStaffDataTable(Request $request)
    {
        $query = StoreStaff::with(['store', 'user'])->orderByDesc('created_at');

        return DataTables::of($query)
            ->addColumn('store_name', function (StoreStaff $staff) {
                return $staff->store?->name ?? '-';
            })
            ->addColumn('user_name', function (StoreStaff $staff) {
                return $staff->user?->first_name . ' ' . $staff->user?->last_name ?? '-';
            })
            ->editColumn('status', function (StoreStaff $staff) {
                return ucfirst($staff->status);
            })
            ->editColumn('hired_at', function (StoreStaff $staff) {
                return $staff->hired_at ? $staff->hired_at->format('d M Y') : '-';
            })
            ->editColumn('created_at', function (StoreStaff $staff) {
                return $staff->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (StoreStaff $staff) {
                return view('components.action-buttons', [
                    'id' => $staff->id,
                    'edit' => 'storeStaffEdit',
                    'delete' => 'storeStaffDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveStoreStaff(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $staffId = $data['staff_id'] ?? null;
                unset($data['staff_id']);

                if ($staffId) {
                    $staff = StoreStaff::findOrFail($staffId);
                    $staff->update($data);
                    $message = 'Staff updated successfully.';
                } else {
                    $staff = StoreStaff::create($data);
                    $message = 'Staff created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'staff' => $staff->fresh(['store', 'user']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving staff: ' . $e->getMessage(),
            ];
        }
    }

    public function getStoreStaffById(int $id): array
    {
        try {
            $staff = StoreStaff::with(['store', 'user'])->findOrFail($id);
            return [
                'status' => 'success',
                'staff' => $staff,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Staff not found.',
            ];
        }
    }

    public function deleteStoreStaff(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $staff = StoreStaff::findOrFail($id);
                $staff->delete();
                return [
                    'status' => 'success',
                    'message' => 'Staff deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting staff: ' . $e->getMessage(),
            ];
        }
    }
}