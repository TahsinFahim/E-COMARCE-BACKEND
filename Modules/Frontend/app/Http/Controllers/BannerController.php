<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Frontend\Services\BannerService;
use Modules\Frontend\Http\Requests\StoreBannerRequest;
use Modules\Frontend\Http\Requests\UpdateBannerRequest;

class BannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Display the banners management page.
     */
    public function index()
    {
        return view('frontend::banners');
    }

    /**
     * Get banner data for DataTable.
     */
    public function dataTable(Request $request)
    {
        return $this->bannerService->getBannerDataTable($request);
    }

    /**
     * Store a newly created banner.
     */
    public function store(StoreBannerRequest $request)
    {
        $result = $this->bannerService->saveBanner($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Display the specified banner.
     */
    public function show($id)
    {
        $result = $this->bannerService->getBannerById($id);
        return response()->json($result);
    }

    /**
     * Update the specified banner.
     */
    public function update(UpdateBannerRequest $request, $id)
    {
        $data = $request->validated();
        $data['banner_id'] = $id;
        $result = $this->bannerService->saveBanner($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Remove the specified banner.
     */
    public function destroy($id)
    {
        $result = $this->bannerService->deleteBanner($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}