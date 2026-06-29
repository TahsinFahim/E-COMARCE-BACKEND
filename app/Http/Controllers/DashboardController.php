<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the dashboard with real database data.
     */
    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('dashboard', [
            'kpi' => $data['kpi'],
            'monthlyRevenue' => $data['monthlyRevenue'],
            'revenueBySource' => $data['revenueBySource'],
            'deliveryOverview' => $data['deliveryOverview'],
            'recentOrders' => $data['recentOrders'],
            'topSellingProducts' => $data['topSellingProducts'],
            'lowStockProducts' => $data['lowStockProducts'],
            'recentActivities' => $data['recentActivities'],
            'inventorySummary' => $data['inventorySummary'],
        ]);
    }

    /**
     * API endpoint to get fresh dashboard data (for AJAX refresh).
     */
    public function apiData()
    {
        $data = $this->dashboardService->getDashboardData();
        return response()->json($data);
    }
}
