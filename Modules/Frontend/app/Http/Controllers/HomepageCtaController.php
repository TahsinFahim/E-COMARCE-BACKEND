<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Frontend\Services\HomepageCtaService;
use Modules\Frontend\Http\Requests\StoreHomepageCtaRequest;
use Modules\Frontend\Http\Requests\UpdateHomepageCtaRequest;

class HomepageCtaController extends Controller
{
    protected HomepageCtaService $ctaService;

    public function __construct(HomepageCtaService $ctaService)
    {
        $this->ctaService = $ctaService;
    }

    /**
     * Display the CTA management page.
     */
    public function index()
    {
        return view('frontend::homepage-ctas');
    }

    /**
     * Get CTA data for DataTable.
     */
    public function dataTable(Request $request)
    {
        return $this->ctaService->getCtaDataTable($request);
    }

    /**
     * Store a newly created CTA.
     */
    public function store(StoreHomepageCtaRequest $request)
    {
        $result = $this->ctaService->saveCta($request->validated());
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Display the specified CTA.
     */
    public function show($id)
    {
        $result = $this->ctaService->getCtaById($id);
        return response()->json($result);
    }

    /**
     * Update the specified CTA.
     */
    public function update(UpdateHomepageCtaRequest $request, $id)
    {
        $data = $request->validated();
        $data['cta_id'] = $id;
        $result = $this->ctaService->saveCta($data);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }

    /**
     * Remove the specified CTA.
     */
    public function destroy($id)
    {
        $result = $this->ctaService->deleteCta($id);
        return response()->json($result, $result['status'] === 'success' ? 200 : 500);
    }
}