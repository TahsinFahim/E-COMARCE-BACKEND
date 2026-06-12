<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Store\Services\AppSettingService;
use Modules\Store\Http\Requests\AppSettingRequest;

class AppSettingController extends Controller
{
    protected AppSettingService $appSettingService;

    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
    }

    public function index()
    {
        return view('store::app-settings.index');
    }

    public function dataTable(Request $request)
    {
        return $this->appSettingService->getAppSettingDataTable($request);
    }

    public function store(AppSettingRequest $request)
    {
        $result = $this->appSettingService->saveAppSetting($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function show($id)
    {
        $result = $this->appSettingService->getAppSettingById($id);
        return response()->json($result);
    }

    public function update(AppSettingRequest $request, $id)
    {
        $data = $request->validated();
        $data['setting_id'] = $id;
        $result = $this->appSettingService->saveAppSetting($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->appSettingService->deleteAppSetting($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}