<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminStatsService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private AdminStatsService $stats,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', $this->stats->overview() + [
            'stats' => $this->stats,
        ]);
    }
}
