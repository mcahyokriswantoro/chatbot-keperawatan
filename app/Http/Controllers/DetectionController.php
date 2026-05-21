<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScreeningIdentityRequest;
use App\Models\ScreeningIdentity;
use App\Models\Wilayah;
use App\Services\TbParuScoringService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DetectionController extends Controller
{
    public function index(): View
    {
        return view('detection.menu', [
            'diseases' => config('diseases'),
        ]);
    }

    public function show(string $disease): View|RedirectResponse
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);

        $diseaseConfig = $diseases[$disease];

        if (! empty($diseaseConfig['requires_identity'])) {
            return view('detection.identity', [
                'disease' => $disease,
                'diseaseLabel' => $diseaseConfig['label'],
                'provinces' => Wilayah::provinsi()->orderBy('nama')->get(['kode', 'nama']),
                'oldWilayah' => [
                    'province_kode' => old('province_kode'),
                    'regency_kode' => old('regency_kode'),
                    'district_kode' => old('district_kode'),
                ],
            ]);
        }

        return $this->chatView($disease, $diseaseConfig);
    }

    public function chat(string $disease): View|RedirectResponse
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);

        $diseaseConfig = $diseases[$disease];

        if (! empty($diseaseConfig['requires_identity']) && ! session("screening_identity.{$disease}")) {
            return redirect()->route('detection.chat', $disease);
        }

        return $this->chatView($disease, $diseaseConfig);
    }

    public function storeIdentity(StoreScreeningIdentityRequest $request, string $disease): RedirectResponse
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);
        abort_unless(! empty($diseases[$disease]['requires_identity']), 404);

        $dateOfBirth = Carbon::parse($request->validated('date_of_birth'));

        $province = Wilayah::findOrFail($request->validated('province_kode'));
        $regency = Wilayah::findOrFail($request->validated('regency_kode'));
        $district = Wilayah::findOrFail($request->validated('district_kode'));

        $identity = ScreeningIdentity::create([
            'user_id' => $request->user()?->id,
            'disease' => $disease,
            'name' => $request->validated('name'),
            'gender' => $request->validated('gender'),
            'phone' => $request->validated('phone'),
            'date_of_birth' => $dateOfBirth,
            'age' => $dateOfBirth->age,
            'weight_kg' => $request->validated('weight_kg'),
            'height_cm' => $request->validated('height_cm'),
            'domicile_address' => $request->validated('domicile_address'),
            'occupation' => $request->validated('occupation'),
            'address' => $request->validated('domicile_address'),
            'province' => $province->nama,
            'province_kode' => $province->kode,
            'regency' => $regency->nama,
            'regency_kode' => $regency->kode,
            'district' => $district->nama,
            'district_kode' => $district->kode,
        ]);

        session(["screening_identity.{$disease}" => $identity->id]);

        return redirect()->route('detection.chat.session', $disease);
    }

    private function chatView(string $disease, array $diseaseConfig): View
    {
        $questions = $diseaseConfig['questions'];
        $maxScore = null;

        $scoringItems = null;

        if ($disease === 'tb_paru') {
            $tbScoring = app(TbParuScoringService::class);
            $questions = $tbScoring->questions();
            $maxScore = $tbScoring->maxScore();
            $scoringItems = config('tb_paru_skrining.items');
        }

        $screening = [
            'bot_name' => config('screening.bot_name'),
            'disease' => $disease,
            'disease_label' => $diseaseConfig['label'],
            'welcome' => $diseaseConfig['welcome'],
            'start_options' => config('screening.start_options'),
            'questions' => $questions,
            'scoring' => $diseaseConfig['scoring'] ?? false,
            'max_score' => $maxScore,
            'scoring_items' => $scoringItems,
            'screening_identity_id' => session("screening_identity.{$disease}"),
            'result' => [
                'title' => 'Skrining '.$diseaseConfig['label'].' Selesai',
                'message' => $disease === 'tb_paru'
                    ? 'Terima kasih telah menyelesaikan skrining TB Paru. Berikut total skor dan ringkasan jawaban Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila skor tinggi atau ada gejala memberat.'
                    : 'Terima kasih telah menyelesaikan skrining '.$diseaseConfig['label'].'. Berikut ringkasan jawaban Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan jika keluhan memberat.',
            ],
        ];

        return view('detection.chat', compact('screening'));
    }
}
