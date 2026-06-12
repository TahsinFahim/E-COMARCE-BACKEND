<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Services\CategoryService;
use Modules\Catalog\Http\Requests\StoreCategoryRequest;
use Modules\Catalog\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $parents = $this->categoryService->getParentCategories();
        return view('catalog::categories', compact('parents'));
    }

    public function dataTable(Request $request)
    {
        return $this->categoryService->getCategoryDataTable($request);
    }

    public function store(StoreCategoryRequest $request)
    {
        $result = $this->categoryService->saveCategory($request->validated());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->categoryService->getCategoryById($id);
        return response()->json($result);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $data = $request->validated();
        $data['category_id'] = $id;
        $result = $this->categoryService->saveCategory($data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->categoryService->deleteCategory($id);
        return response()->json($result);
    }
}
