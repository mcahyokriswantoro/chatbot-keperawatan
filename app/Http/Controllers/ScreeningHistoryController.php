<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ScreeningHistoryController extends Controller
{
    public function index(): View
    {
        $sessions = auth()->user()
            ->screeningSessions()
            ->latest()
            ->paginate(10);

        return view('history.index', compact('sessions'));
    }

    public function show(int $id): View
    {
        $session = auth()->user()->screeningSessions()->findOrFail($id);

        return view('history.show', compact('session'));
    }
}
