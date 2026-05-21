<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EmergencyController extends Controller
{
    public function index(): View
    {
        return view('emergency.index', [
            'hotlines' => config('emergency.hotlines'),
            'warnings' => config('emergency.warnings'),
        ]);
    }
}
