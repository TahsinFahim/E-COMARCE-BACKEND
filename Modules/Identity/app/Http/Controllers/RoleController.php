<?php

namespace Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Identity\Services\RoleService;
use Modules\Identity\Services\PermissionService;
use Modules\Identity\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    protected RoleService $roleService;
    protected PermissionService $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        
        // Group permissions by module (e.g., "users.view" -> "users")
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return ucfirst($parts[0]);
        })->map(function ($group) {
            return $group->sortBy('name')->values();
        })->sortKeys();

        return view('identity::roles.index', compact('groupedPermissions'));
    }

    public function dataTable(Request $request)
    {
        return $this->roleService->getRoleDataTable($request);
    }

    public function store(RoleRequest $request)
    {
        $result = $this->roleService->saveRole($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->roleService->getRoleById($id);
        return response()->json($result);
    }

    public function update(RoleRequest $request, $id)
    {
        $data = $request->validated();
        $data['role_id'] = $id;
        $result = $this->roleService->saveRole($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->roleService->deleteRole($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}