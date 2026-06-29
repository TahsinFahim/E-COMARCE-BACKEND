<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Frontend\Services\SettingService;

class SiteSettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index()
    {
        $grouped = $this->settingService->getGrouped();
        $groups = ['general', 'social', 'contact', 'seo'];
        return view('frontend::site-settings', compact('grouped', 'groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        // Clear cache first so we get fresh DB data
        $this->settingService->clearCache();

        // Fetch settings directly from DB (bypass cache)
        $settings = \Modules\Frontend\Models\Setting::all()->keyBy('key');
        $data = $request->except('_token', '_method');
        $updateData = [];

        foreach ($data as $key => $value) {
            if (isset($settings[$key])) {
                $type = $settings[$key]->type;

                if ($type === 'image' && $request->hasFile($key)) {
                    $value = $this->settingService->uploadImage($request->file($key));
                }

                $typeRules = SettingService::TYPES;
                $rules = [$key => $typeRules[$type]['validation'] ?? 'nullable|string'];

                $validator = Validator::make([$key => $value], $rules);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $updateData[$key] = $value;
            }
        }

        if (!empty($updateData)) {
            $this->settingService->updateBulk($updateData);
        }

        return redirect()->route('frontend.site-settings.index')
            ->with('success', 'Site settings updated successfully!');
    }

    public function seed(): RedirectResponse
    {
        $this->settingService->seedDefaults();
        return redirect()->route('frontend.site-settings.index')
            ->with('success', 'Default settings have been created!');
    }
}