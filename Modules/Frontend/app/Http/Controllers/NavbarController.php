<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Frontend\Services\NavbarService;
use Modules\Frontend\Http\Requests\StoreNavbarItemRequest;
use Modules\Frontend\Http\Requests\UpdateNavbarItemRequest;
use Modules\Frontend\Http\Requests\StoreSubnavbarItemRequest;
use Modules\Frontend\Http\Requests\UpdateSubnavbarItemRequest;

class NavbarController extends Controller
{
    protected NavbarService $navbarService;

    public function __construct(NavbarService $navbarService)
    {
        $this->navbarService = $navbarService;
    }

    /**
     * Display the navbar items management page.
     */
    public function index()
    {
        return view('frontend::nav-items');
    }

    /**
     * Display the subnavbar items management page, optionally filtered by navbar_item_id.
     */
    public function subnavbarIndex()
    {
        return view('frontend::sub-nav-items');
    }

    // ===== Navbar Items =====

    public function navbarDataTable(Request $request)
    {
        return $this->navbarService->getNavbarDataTable($request);
    }

    public function storeNavbarItem(StoreNavbarItemRequest $request)
    {
        $result = $this->navbarService->saveNavbarItem($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function showNavbarItem($id)
    {
        $result = $this->navbarService->getNavbarItemById($id);
        return response()->json($result);
    }

    public function updateNavbarItem(UpdateNavbarItemRequest $request, $id)
    {
        $data = $request->validated();
        $data['navbar_item_id'] = $id;
        $result = $this->navbarService->saveNavbarItem($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroyNavbarItem($id)
    {
        $result = $this->navbarService->deleteNavbarItem($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    // ===== Subnavbar Items =====

    public function subnavbarDataTable(Request $request)
    {
        return $this->navbarService->getSubnavbarDataTable($request);
    }

    public function storeSubnavbarItem(StoreSubnavbarItemRequest $request)
    {
        $result = $this->navbarService->saveSubnavbarItem($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function showSubnavbarItem($id)
    {
        $result = $this->navbarService->getSubnavbarItemById($id);
        return response()->json($result);
    }

    public function updateSubnavbarItem(UpdateSubnavbarItemRequest $request, $id)
    {
        $data = $request->validated();
        $data['subnavbar_item_id'] = $id;
        $result = $this->navbarService->saveSubnavbarItem($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroySubnavbarItem($id)
    {
        $result = $this->navbarService->deleteSubnavbarItem($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Get all active navbar items (for select dropdowns).
     */
    public function getNavbarItemsList()
    {
        $items = $this->navbarService->getAllNavbarItems();
        return response()->json([
            'status' => 'success',
            'navbar_items' => $items,
        ]);
    }
}