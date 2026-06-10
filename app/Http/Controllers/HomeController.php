<?php

namespace App\Http\Controllers;

use App\Services\HealthStatusService;
use App\Services\HealthTipService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(HealthTipService $healthTipService, HealthStatusService $healthStatusService): View
    {
        return view('home', [
            'tips' => $healthTipService->getWeeklyTips(),
            'healthStatus' => $healthStatusService->forUser(auth()->user()),
        ]);
    }
}
