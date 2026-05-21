<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DetectionController extends Controller
{
    public function index(): View
    {
        return view('detection.chat', [
            'screening' => config('screening'),
        ]);
    }
}
