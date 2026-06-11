<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthMonitoring;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $entries = HealthMonitoring::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('recorded_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.monitoring.index', [
            'entries' => $entries,
            'search' => $search,
            'totalEntries' => HealthMonitoring::count(),
        ]);
    }

    public function show(HealthMonitoring $monitoring): View
    {
        $monitoring->load('user');

        return view('admin.monitoring.show', [
            'entry' => $monitoring,
        ]);
    }
}
