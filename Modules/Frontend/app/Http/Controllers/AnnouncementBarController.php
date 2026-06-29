<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Frontend\Services\AnnouncementBarService;
use Modules\Frontend\Http\Requests\StoreAnnouncementBarRequest;
use Modules\Frontend\Http\Requests\UpdateAnnouncementBarRequest;

class AnnouncementBarController extends Controller
{
    protected AnnouncementBarService $announcementBarService;

    public function __construct(AnnouncementBarService $announcementBarService)
    {
        $this->announcementBarService = $announcementBarService;
    }

    /**
     * Display the Announcement Bar management page.
     */
    public function index()
    {
        return view('frontend::announcement-bars');
    }

    /**
     * Get Announcement Bar data for DataTable.
     */
    public function dataTable(Request $request)
    {
        return $this->announcementBarService->getAnnouncementBarDataTable($request);
    }

    /**
     * Store a newly created Announcement Bar.
     */
    public function store(StoreAnnouncementBarRequest $request)
    {
        $result = $this->announcementBarService->saveAnnouncementBar($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Display the specified Announcement Bar.
     */
    public function show($id)
    {
        $result = $this->announcementBarService->getAnnouncementBarById($id);
        return response()->json($result);
    }

    /**
     * Update the specified Announcement Bar.
     */
    public function update(UpdateAnnouncementBarRequest $request, $id)
    {
        $data = $request->validated();
        $data['announcement_bar_id'] = $id;
        $result = $this->announcementBarService->saveAnnouncementBar($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Remove the specified Announcement Bar.
     */
    public function destroy($id)
    {
        $result = $this->announcementBarService->deleteAnnouncementBar($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}