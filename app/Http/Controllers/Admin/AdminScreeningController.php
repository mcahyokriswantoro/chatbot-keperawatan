<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningSession;
use App\Services\AdminStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScreeningController extends Controller
{
    public function __construct(
        private AdminStatsService $stats,
    ) {}

    public function index(Request $request): View
    {
        $disease = $request->query('disease');
        $risk = $request->query('risk');
        $search = trim((string) $request->query('q', ''));

        $screenings = ScreeningSession::query()
            ->with(['user', 'identity'])
            ->when($disease, fn ($q) => $q->where('disease', $disease))
            ->when($risk, fn ($q) => $q->where('risk_level', $risk))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('identity', fn ($i) => $i->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $diseases = ScreeningSession::query()
            ->whereNotNull('disease')
            ->distinct()
            ->orderBy('disease')
            ->pluck('disease');

        return view('admin.screenings.index', [
            'screenings' => $screenings,
            'diseases' => $diseases,
            'filters' => [
                'disease' => $disease,
                'risk' => $risk,
                'q' => $search,
            ],
            'charts' => $this->stats->screeningCharts($disease, $risk, $search),
            'stats' => $this->stats,
        ]);
    }

    public function show(ScreeningSession $screeningSession): View
    {
        $screeningSession->load(['user', 'identity']);

        return view('admin.screenings.show', [
            'session' => $screeningSession,
        ]);
    }
}
