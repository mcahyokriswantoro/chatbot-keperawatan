<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAccessController extends Controller
{
    public function __construct(
        private AdminAccessService $access,
    ) {}

    public function index(): View
    {
        return view('admin.access.index', [
            'admins' => User::query()->where('is_admin', true)->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $user = $this->access->grantByEmail($validated['email']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('admin.access.index')
            ->with('status', "{$user->name} ({$user->email}) sekarang bisa login sebagai admin.");
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->access->revoke($user, auth()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['access' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.access.index')
            ->with('status', "Akses admin untuk {$user->email} telah dicabut.");
    }
}
