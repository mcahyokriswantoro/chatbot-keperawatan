<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EmergencyController extends Controller
{
    public function index(): View
    {
        return view('emergency.index', [
            'hotlines' => config('emergency.hotlines'),
            'categories' => config('emergency.categories'),
            'symptoms' => config('emergency.symptoms'),
            'fast' => config('emergency.fast'),
            'steps' => config('emergency.steps'),
            'warnings' => config('emergency.warnings'),
        ]);
    }
}
