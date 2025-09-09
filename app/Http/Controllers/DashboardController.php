<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $mode = $request->get('mode', 'daily'); // default to daily
        $data = $this->dashboardService->getDashboardData($mode);

        return view('welcome', array_merge($data, ['current_mode' => $mode]));
    }
}
