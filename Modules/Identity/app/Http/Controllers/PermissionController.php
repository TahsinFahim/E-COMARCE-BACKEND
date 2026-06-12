<?php

namespace Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Identity\Services\PermissionService;
use Modules\Identity\Http\Requests\PermissionRequest;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        return view('identity::permissions.index');
    }

    public function dataTable(Request $request)
    {
        return $this->permissionService->getPermissionDataTable($request);
    }

    public function store(PermissionRequest $request)
    {
        $result = $this->permissionService->savePermission($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->permissionService->getPermissionById($id);
        return response()->json($result);
    }

    public function update(PermissionRequest $request, $id)
    {
        $data = $request->validated();
        $data['permission_id'] = $id;
        $result = $this->permissionService->savePermission($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->permissionService->deletePermission($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}