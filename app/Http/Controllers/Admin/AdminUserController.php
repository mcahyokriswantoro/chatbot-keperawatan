<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningSession;
use App\Models\User;
use App\Services\PatientExcelExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $screeningIdentities = $user->screeningIdentities()
            ->with([
                'screeningSessions' => fn ($q) => $q->latest(),
            ])
            ->latest()
            ->get();

        return view('admin.users.show', [
            'user'                => $user,
            'screeningIdentities' => $screeningIdentities,
            'monitoring'          => $user->healthMonitorings()->latest('recorded_at')->limit(20)->get(),
        ]);
    }

    public function export(PatientExcelExportService $exporter): StreamedResponse
    {
        return $exporter->download();
    }

    public function rekap(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $disease = $request->query('disease');
        $risk = $request->query('risk');

        $screenings = ScreeningSession::query()
            ->with(['user', 'identity'])
            ->when($disease, fn ($q) => $q->where('disease', $disease))
            ->when($risk, fn ($q) => $q->where('risk_level', $risk))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('identity', fn ($i) => $i->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->get();

        $groupedPatients = $screenings->groupBy(function ($session) {
            $userId = $session->user_id ?? 'guest';
            $name = $session->identity?->name ?? ($session->user?->name ?? 'Pasien');
            $target = $session->identity?->screening_target ?? 'self';
            return "{$userId}_{$name}_{$target}";
        });

        $diseases = ScreeningSession::query()
            ->whereNotNull('disease')
            ->distinct()
            ->orderBy('disease')
            ->pluck('disease');

        $totalScreenings = ScreeningSession::count();
        $totalHighRisk = ScreeningSession::whereIn('risk_level', ['high', 'emergency'])->count();
        $totalPatients = User::where('is_admin', false)->count();

        return view('admin.users.rekap', [
            'groupedPatients' => $groupedPatients,
            'diseases' => $diseases,
            'filters' => [
                'q' => $search,
                'disease' => $disease,
                'risk' => $risk,
            ],
            'totalScreenings' => $totalScreenings,
            'totalHighRisk' => $totalHighRisk,
            'totalPatients' => $totalPatients,
        ]);
    }
}
