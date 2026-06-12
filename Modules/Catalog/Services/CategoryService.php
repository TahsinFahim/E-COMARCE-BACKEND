<?php

namespace Modules\Catalog\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Models\Category;
use Yajra\DataTables\DataTables;

class CategoryService
{
    public function getParentCategories()
    {
        return Category::whereNull('parent_id')->orderBy('name')->get();
    }

    public function getCategoryDataTable(Request $request)
    {
        $query = Category::with('parent')->orderByDesc('created_at');

        return DataTables::of($query)
            ->addColumn('parent', function (Category $category) {
                return $category->parent?->name ?: '-';
            })
            ->editColumn('status', function (Category $category) {
                return ucfirst($category->status);
            })
            ->editColumn('created_at', function (Category $category) {
                return $category->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Category $category) {
                return view('components.action-buttons', [
                    'id' => $category->id,
                    'edit' => 'categoryEdit',
                    'delete' => 'categoryDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveCategory(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $categoryId = $data['category_id'] ?? null;
                $data['parent_id'] = $data['parent_id'] ?: null;
                $data['status'] = $data['status'] ?? 'active';

                if ($categoryId) {
                    $category = Category::findOrFail($categoryId);
                    $category->update($data);
                    $message = 'Category updated successfully.';
                } else {
                    $category = Category::create($data);
                    $message = 'Category created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'category' => $category->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving category: ' . $e->getMessage(),
            ];
        }
    }

    public function getCategoryById(int $id): array
    {
        try {
            $category = Category::findOrFail($id);
            return [
                'status' => 'success',
                'category' => $category,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Category not found.',
            ];
        }
    }

    public function deleteCategory(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $category = Category::findOrFail($id);
                $category->delete();

                return [
                    'status' => 'success',
                    'message' => 'Category deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting category: ' . $e->getMessage(),
            ];
        }
    }
}
