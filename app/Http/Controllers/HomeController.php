<?php

namespace App\Http\Controllers;

use App\Services\HealthStatusService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(HealthStatusService $healthStatusService): View
    {
        return view('home', [
            'tips' => config('health.chatbot_tips'),
            'healthStatus' => $healthStatusService->forUser(auth()->user()),
        ]);
    }
}
