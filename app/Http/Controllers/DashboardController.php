<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('dashboard.index', [
            'screeningCount' => $user->screeningSessions()->count(),
            'latestScreening' => $user->screeningSessions()->latest()->first(),
            'monitoringCount' => $user->healthMonitorings()->count(),
            'latestMonitoring' => $user->healthMonitorings()->latest('recorded_at')->first(),
            'pendingTasks' => $user->selfManagementLogs()->where('completed', false)->count(),
        ]);
    }
}
