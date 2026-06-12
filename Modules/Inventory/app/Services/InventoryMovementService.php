<?php

namespace Modules\Inventory\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\InventoryMovement;
use Yajra\DataTables\DataTables;

class InventoryMovementService
{
    public function getMovementDataTable(Request $request)
    {
        $query = InventoryMovement::query()
            ->with(['location', 'createdBy'])
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->addColumn('location_name', function (InventoryMovement $movement) {
                return $movement->location ? $movement->location->name : '-';
            })
            ->editColumn('movement_type', function (InventoryMovement $movement) {
                return ucfirst(str_replace('_', ' ', $movement->movement_type));
            })
            ->editColumn('quantity', function (InventoryMovement $movement) {
                return $movement->quantity > 0
                    ? '<span class="text-green-600">+' . number_format($movement->quantity) . '</span>'
                    : '<span class="text-red-600">' . number_format($movement->quantity) . '</span>';
            })
            ->addColumn('created_by_name', function (InventoryMovement $movement) {
                return $movement->createdBy ? $movement->createdBy->name : 'System';
            })
            ->editColumn('created_at', function (InventoryMovement $movement) {
                return $movement->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (InventoryMovement $movement) {
                return view('components.action-buttons', [
                    'id' => $movement->id,
                    'edit' => 'movementEdit',
                    'delete' => 'movementDelete',
                ])->render();
            })
            ->rawColumns(['quantity', 'action'])
            ->make(true);
    }

    public function saveMovement(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $movementId = $data['movement_id'] ?? null;
                unset($data['movement_id']);

                if (!isset($data['created_by'])) {
                    $data['created_by'] = auth()->id();
                }

                if ($movementId) {
                    $movement = InventoryMovement::findOrFail($movementId);
                    $movement->update($data);
                    $message = 'Movement updated successfully.';
                } else {
                    $movement = InventoryMovement::create($data);
                    $message = 'Movement created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'movement' => $movement->fresh()->load(['location', 'createdBy']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving movement: ' . $e->getMessage(),
            ];
        }
    }

    public function getMovementById(int $id): array
    {
        try {
            $movement = InventoryMovement::with(['location', 'createdBy'])->findOrFail($id);
            return [
                'status' => 'success',
                'movement' => $movement,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Movement not found.',
            ];
        }
    }

    public function deleteMovement(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $movement = InventoryMovement::findOrFail($id);
                $movement->delete();
                return [
                    'status' => 'success',
                    'message' => 'Movement deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting movement: ' . $e->getMessage(),
            ];
        }
    }
}