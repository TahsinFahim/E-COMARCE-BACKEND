<?php

namespace Modules\Store\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Store\Models\Store;
use Yajra\DataTables\DataTables;

class StoreService
{
    public function getStoreDataTable(Request $request)
    {
        $query = Store::query()->orderByDesc('created_at');

        return DataTables::of($query)
            ->editColumn('status', function (Store $store) {
                return ucfirst($store->status);
            })
            ->editColumn('created_at', function (Store $store) {
                return $store->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Store $store) {
                return view('components.action-buttons', [
                    'id' => $store->id,
                    'edit' => 'storeEdit',
                    'delete' => 'storeDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveStore(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $storeId = $data['store_id'] ?? null;

                if (!isset($data['slug']) && isset($data['name'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                unset($data['store_id']);

                if ($storeId) {
                    $store = Store::findOrFail($storeId);
                    $store->update($data);
                    $message = 'Store updated successfully.';
                } else {
                    $store = Store::create($data);
                    $message = 'Store created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'store' => $store->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving store: ' . $e->getMessage(),
            ];
        }
    }

    public function getStoreById(int $id): array
    {
        try {
            $store = Store::findOrFail($id);
            return [
                'status' => 'success',
                'store' => $store,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Store not found.',
            ];
        }
    }

    public function deleteStore(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $store = Store::findOrFail($id);
                $store->delete();

                return [
                    'status' => 'success',
                    'message' => 'Store deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting store: ' . $e->getMessage(),
            ];
        }
    }

    public function getAllActiveStores(): array
    {
        return Store::where('status', 'active')->orderBy('name')->get()->toArray();
    }
}