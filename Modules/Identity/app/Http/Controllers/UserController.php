<?php

namespace Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Identity\Services\UserService;
use Modules\Identity\Services\RoleService;
use Modules\Identity\Http\Requests\UserRequest;

class UserController extends Controller
{
    protected UserService $userService;
    protected RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return view('identity::users.index', compact('roles'));
    }

    public function dataTable(Request $request)
    {
        return $this->userService->getUserDataTable($request);
    }

    public function store(UserRequest $request)
    {
        $result = $this->userService->saveUser($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->userService->getUserById($id);
        return response()->json($result);
    }

    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        $data['user_id'] = $id;
        $result = $this->userService->saveUser($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}