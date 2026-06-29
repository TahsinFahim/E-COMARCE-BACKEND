<?php

namespace Modules\Catalog\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Catalog\Models\ProductRequest;
use Yajra\DataTables\DataTables;

class ProductRequestService
{
    public function getProductRequestDataTable(Request $request)
    {
        $query = ProductRequest::query()->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->editColumn('status', function (ProductRequest $pr) {
                $colors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'fulfilled' => 'bg-blue-100 text-blue-800',
                ];
                $color = $colors[$pr->status] ?? 'bg-gray-100 text-gray-800';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . ucfirst($pr->status) . '</span>';
            })
            ->editColumn('created_at', function (ProductRequest $pr) {
                return $pr->created_at->format('d M Y H:i');
            })
            ->addColumn('product_image_preview', function (ProductRequest $pr) {
                if ($pr->product_image) {
                    return '<img src="' . $pr->product_image . '" class="h-10 w-10 rounded object-cover" />';
                }
                return '-';
            })
            ->addColumn('action', function (ProductRequest $pr) {
                $viewBtn = '<button onclick="viewProductRequest(' . $pr->id . ')" class="bg-blue-600 text-white px-2 py-1 rounded text-sm hover:bg-blue-500 mr-1" title="View"><i class="fa fa-eye"></i></button>';
                $statusBtn = '<button onclick="changeProductRequestStatus(' . $pr->id . ')" class="bg-green-600 text-white px-2 py-1 rounded text-sm hover:bg-green-500" title="Change Status"><i class="fa fa-check-circle"></i></button>';
                return $viewBtn . $statusBtn;
            })
            ->rawColumns(['status', 'product_image_preview', 'action'])
            ->make(true);
    }

    public function getProductRequestById(int $id): array
    {
        try {
            $productRequest = ProductRequest::with(['user', 'product'])->findOrFail($id);
            return [
                'status' => 'success',
                'product_request' => $productRequest,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Product request not found.',
            ];
        }
    }

    public function updateStatus(int $id, string $status): array
    {
        try {
            $productRequest = ProductRequest::findOrFail($id);
            $productRequest->update(['status' => $status]);

            return [
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'product_request' => $productRequest->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error updating status: ' . $e->getMessage(),
            ];
        }
    }

    public function storeFromFrontend(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                // Handle image upload
                if (isset($data['product_image']) && $data['product_image'] instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = 'product-request-' . time() . '.' . $data['product_image']->getClientOriginalExtension();
                    $path = $data['product_image']->storeAs('product-requests', $fileName, 'public');
                    $data['product_image'] = $path;
                }

                $productRequest = ProductRequest::create($data);

                return [
                    'status' => 'success',
                    'message' => 'Your product request has been submitted successfully. We will review it shortly.',
                    'product_request' => $productRequest,
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error submitting request: ' . $e->getMessage(),
            ];
        }
    }
}