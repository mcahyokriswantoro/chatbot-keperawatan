<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'screeningCount' => ScreeningSession::count(),
            'emergencyCount' => ScreeningSession::where('is_emergency', true)->count(),
            'articleCount' => HealthArticle::count(),
            'recentScreenings' => ScreeningSession::with('user')->latest()->limit(10)->get(),
        ]);
    }
}
