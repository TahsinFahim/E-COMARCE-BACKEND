<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Frontend\Services\SettingService;

class SettingsApiController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index(): JsonResponse
    {
        $settings = $this->settingService->getGrouped();
        $flat = [];
        foreach ($settings as $group => $items) {
            foreach ($items as $item) {
                $flat[$item['key']] = $item['value'];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $flat,
        ]);
    }
}