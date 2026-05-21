<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function index(): View
    {
        return view('profile.index');
    }
}
