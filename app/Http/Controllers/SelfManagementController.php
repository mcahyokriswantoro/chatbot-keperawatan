<?php

namespace App\Http\Controllers;

use App\Models\SelfManagementLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelfManagementController extends Controller
{
    public function index(): View
    {
        $logs = auth()->user()
            ->selfManagementLogs()
            ->latest()
            ->paginate(10);

        $activities = config('self_management.activities');

        return view('self-management.index', compact('logs', 'activities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'activity_type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'scheduled_for' => ['nullable', 'date'],
        ]);

        auth()->user()->selfManagementLogs()->create($validated);

        return back()->with('status', 'Aktivitas berhasil ditambahkan.');
    }

    public function toggle(SelfManagementLog $log): RedirectResponse
    {
        abort_unless($log->user_id === auth()->id(), 403);

        $log->update(['completed' => ! $log->completed]);

        return back();
    }
}
