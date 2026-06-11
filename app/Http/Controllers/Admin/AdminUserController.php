<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('is_admin', false)
            ->withCount(['screeningSessions', 'healthMonitorings'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'totalUsers' => User::query()->where('is_admin', false)->count(),
        ]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $user->loadCount(['screeningSessions', 'healthMonitorings']);

        return view('admin.users.show', [
            'user' => $user,
            'screenings' => $user->screeningSessions()->with('identity')->latest()->limit(20)->get(),
            'monitoring' => $user->healthMonitorings()->latest('recorded_at')->limit(20)->get(),
        ]);
    }
}
