<?php

namespace App\Http\Controllers;

use App\Models\SelfManagementLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelfManagementLogController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'activity_type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'scheduled_for' => ['required', 'date'],
        ]);

        auth()->user()->selfManagementLogs()->create([
            ...$validated,
            'completed' => false,
        ]);

        return back()->with('status', 'Pengingat aktivitas berhasil ditambahkan.');
    }

    public function toggle(SelfManagementLog $log): RedirectResponse
    {
        abort_unless($log->user_id == auth()->id(), 403);

        $log->update(['completed' => ! $log->completed]);

        return back()->with('status', $log->completed ? 'Aktivitas ditandai selesai.' : 'Aktivitas ditandai belum selesai.');
    }
}
