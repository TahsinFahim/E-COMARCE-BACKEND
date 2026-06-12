<?php

namespace Modules\Reviews\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Reviews\Models\ProductReview;
use Yajra\DataTables\DataTables;

class ProductReviewService
{
    public function getReviewDataTable(Request $request)
    {
        $query = ProductReview::query()->with(['product', 'user'])->orderByDesc('created_at');

        return DataTables::of($query)
            ->editColumn('rating', fn($r) => str_repeat('★', $r->rating) . str_repeat('☆', 5 - $r->rating))
            ->editColumn('status', fn($r) => ucfirst($r->status))
            ->addColumn('product_name', fn($r) => $r->product ? $r->product->name : '-')
            ->addColumn('user_name', fn($r) => $r->user ? $r->user->name : '-')
            ->editColumn('created_at', fn($r) => $r->created_at->format('d M Y H:i'))
            ->addColumn('action', fn($r) => view('components.action-buttons', [
                'id' => $r->id, 'edit' => 'productReviewEdit', 'delete' => 'productReviewDelete',
            ])->render())
            ->rawColumns(['action', 'rating'])
            ->make(true);
    }

    public function saveReview(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $id = $data['review_id'] ?? null; unset($data['review_id']);
                if ($id) { $item = ProductReview::findOrFail($id); $item->update($data); $msg = 'Review updated.'; }
                else { $item = ProductReview::create($data); $msg = 'Review created.'; }
                return ['status' => 'success', 'message' => $msg, 'review' => $item->fresh()->load(['product', 'user'])];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getReviewById(int $id): array
    {
        try { $item = ProductReview::with(['product', 'user'])->findOrFail($id); return ['status' => 'success', 'review' => $item]; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Review not found.']; }
    }

    public function deleteReview(int $id): array
    {
        try { ProductReview::findOrFail($id)->delete(); return ['status' => 'success', 'message' => 'Review deleted.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }

    public function approveReview(int $id): array
    {
        try { ProductReview::findOrFail($id)->update(['status' => 'approved']); return ['status' => 'success', 'message' => 'Review approved.']; }
        catch (\Exception $e) { return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
    }
}