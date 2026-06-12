<?php

namespace Modules\Inventory\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\InventoryLocation;
use Yajra\DataTables\DataTables;

class InventoryLocationService
{
    public function getLocationDataTable(Request $request)
    {
        $query = InventoryLocation::query()->with('store')->orderByDesc('created_at');

        return DataTables::of($query)
            ->editColumn('status', function (InventoryLocation $location) {
                return ucfirst($location->status);
            })
            ->editColumn('location_type', function (InventoryLocation $location) {
                return ucfirst(str_replace('_', ' ', $location->location_type));
            })
            ->addColumn('store_name', function (InventoryLocation $location) {
                return $location->store ? $location->store->name : '-';
            })
            ->editColumn('created_at', function (InventoryLocation $location) {
                return $location->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (InventoryLocation $location) {
                return view('components.action-buttons', [
                    'id' => $location->id,
                    'edit' => 'locationEdit',
                    'delete' => 'locationDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveLocation(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $locationId = $data['location_id'] ?? null;
                unset($data['location_id']);

                if ($locationId) {
                    $location = InventoryLocation::findOrFail($locationId);
                    $location->update($data);
                    $message = 'Location updated successfully.';
                } else {
                    $location = InventoryLocation::create($data);
                    $message = 'Location created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'location' => $location->fresh()->load('store'),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving location: ' . $e->getMessage(),
            ];
        }
    }

    public function getLocationById(int $id): array
    {
        try {
            $location = InventoryLocation::with('store')->findOrFail($id);
            return [
                'status' => 'success',
                'location' => $location,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Location not found.',
            ];
        }
    }

    public function deleteLocation(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $location = InventoryLocation::findOrFail($id);
                $location->delete();
                return [
                    'status' => 'success',
                    'message' => 'Location deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting location: ' . $e->getMessage(),
            ];
        }
    }

    public function getAllActiveLocations(): array
    {
        return InventoryLocation::where('status', 'active')
            ->with('store')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}