<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScreeningIdentityRequest;
use App\Models\ScreeningIdentity;
use App\Models\Wilayah;
use App\Services\DhfScoringService;
use App\Services\DiabetesMelitusScoringService;
use App\Services\HipertensiScoringService;
use App\Services\JantungKoronerScoringService;
use App\Services\PenyakitGinjalScoringService;
use App\Services\PpokScoringService;
use App\Services\StrokeScoringService;
use App\Services\TbParuScoringService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DetectionController extends Controller
{
    public function identityForm(): View
    {
        return view('detection.identity', [
            'provinces' => Wilayah::provinsi()->orderBy('nama')->get(['kode', 'nama']),
            'oldWilayah' => [
                'province_kode' => old('province_kode'),
                'regency_kode' => old('regency_kode'),
                'district_kode' => old('district_kode'),
            ],
        ]);
    }

    public function storeIdentity(StoreScreeningIdentityRequest $request): RedirectResponse
    {
        $dateOfBirth = Carbon::parse($request->validated('date_of_birth'));

        $province = Wilayah::findOrFail($request->validated('province_kode'));
        $regency = Wilayah::findOrFail($request->validated('regency_kode'));
        $district = Wilayah::findOrFail($request->validated('district_kode'));

        $identity = ScreeningIdentity::create([
            'user_id' => $request->user()?->id,
            'disease' => 'general',
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

        session(['screening_identity_id' => $identity->id]);

        return redirect()->route('detection.start');
    }

    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNoIdentity()) {
            return $redirect;
        }

        return view('detection.menu', [
            'diseases' => config('diseases'),
        ]);
    }

    public function show(string $disease): View|RedirectResponse
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);

        if ($redirect = $this->redirectIfNoIdentity()) {
            return $redirect;
        }

        if (in_array($disease, ['tb_paru', 'dhf', 'ppok', 'penyakit_ginjal', 'stroke', 'jantung_koroner', 'diabetes_melitus', 'hipertensi'], true)) {
            return redirect()->route('detection.chat.session', $disease);
        }

        return $this->chatView($disease, $diseases[$disease]);
    }

    public function chat(string $disease): View|RedirectResponse
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);

        if ($redirect = $this->redirectIfNoIdentity()) {
            return $redirect;
        }

        return $this->chatView($disease, $diseases[$disease]);
    }

    private function redirectIfNoIdentity(): ?RedirectResponse
    {
        if (! session('screening_identity_id')) {
            return redirect()->route('detection.identity');
        }

        return null;
    }

    private function chatView(string $disease, array $diseaseConfig): View
    {
        $questions = $diseaseConfig['questions'];
        $maxScore = null;
        $scoringItems = null;

        $scoringLegend = null;
        $questionPrefix = null;
        $warningSignIds = null;

        if ($disease === 'tb_paru') {
            $tbScoring = app(TbParuScoringService::class);
            $questions = $tbScoring->questions();
            $maxScore = $tbScoring->maxScore();
            $scoringItems = config('tb_paru_skrining.items');
            $scoringLegend = '≥11 Tinggi · 6–10 Sedang · 0–5 Rendah';
        } elseif ($disease === 'dhf') {
            $dhfScoring = app(DhfScoringService::class);
            $questions = $dhfScoring->questions();
            $maxScore = $dhfScoring->maxScore();
            $scoringItems = config('dhf_skrining.items');
            $questionPrefix = config('dhf_skrining.question_prefix');
            $warningSignIds = config('dhf_skrining.warning_sign_ids');
            $scoringLegend = config('dhf_skrining.scoring_legend');
        } elseif ($disease === 'ppok') {
            $ppokScoring = app(PpokScoringService::class);
            $questions = $ppokScoring->questions();
            $maxScore = $ppokScoring->maxScore();
            $scoringItems = config('ppok_skrining.items');
            $questionPrefix = config('ppok_skrining.question_prefix');
            $scoringLegend = config('ppok_skrining.scoring_legend');
        } elseif ($disease === 'penyakit_ginjal') {
            $ginjalScoring = app(PenyakitGinjalScoringService::class);
            $questions = $ginjalScoring->questions();
            $maxScore = $ginjalScoring->maxScore();
            $scoringItems = config('penyakit_ginjal_skrining.items');
            $questionPrefix = config('penyakit_ginjal_skrining.question_prefix');
            $scoringLegend = config('penyakit_ginjal_skrining.scoring_legend');
        } elseif ($disease === 'stroke') {
            $strokeScoring = app(StrokeScoringService::class);
            $questions = $strokeScoring->questions();
            $maxScore = $strokeScoring->maxScore();
            $scoringItems = config('stroke_skrining.items');
            $questionPrefix = config('stroke_skrining.question_prefix');
            $scoringLegend = config('stroke_skrining.scoring_legend');
        } elseif ($disease === 'jantung_koroner') {
            $jantungScoring = app(JantungKoronerScoringService::class);
            $questions = $jantungScoring->questions();
            $maxScore = $jantungScoring->maxScore();
            $scoringItems = config('jantung_koroner_skrining.items');
            $questionPrefix = config('jantung_koroner_skrining.question_prefix');
            $scoringLegend = config('jantung_koroner_skrining.scoring_legend');
        } elseif ($disease === 'diabetes_melitus') {
            $dmScoring = app(DiabetesMelitusScoringService::class);
            $questions = $dmScoring->questions();
            $maxScore = $dmScoring->maxScore();
            $scoringItems = config('diabetes_melitus_skrining.items');
            $questionPrefix = config('diabetes_melitus_skrining.question_prefix');
            $scoringLegend = config('diabetes_melitus_skrining.scoring_legend');
        } elseif ($disease === 'hipertensi') {
            $htScoring = app(HipertensiScoringService::class);
            $questions = $htScoring->questions();
            $maxScore = $htScoring->maxScore();
            $scoringItems = config('hipertensi_skrining.items');
            $questionPrefix = config('hipertensi_skrining.question_prefix');
            $scoringLegend = config('hipertensi_skrining.scoring_legend');
        }

        $resultMessages = [
            'tb_paru' => 'Terima kasih telah menyelesaikan skrining TB Paru. Berikut total skor dan ringkasan jawaban Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila skor tinggi atau ada gejala memberat.',
            'dhf' => 'Terima kasih telah menyelesaikan skrining DHF. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera ke fasilitas kesehatan bila risiko tinggi atau ada tanda peringatan.',
            'ppok' => 'Terima kasih telah menyelesaikan skrining PPOK. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila risiko tinggi atau gejala memberat.',
            'penyakit_ginjal' => 'Terima kasih telah menyelesaikan skrining Penyakit Ginjal. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila risiko tinggi atau gejala memberat.',
            'stroke' => 'Terima kasih telah menyelesaikan skrining Stroke. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Bila ada gejala mendadak (FAST), segera hubungi layanan darurat atau kunjungi IGD.',
            'jantung_koroner' => 'Terima kasih telah menyelesaikan skrining Jantung Koroner. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Bila nyeri dada hebat atau tidak membaik, segera ke IGD.',
            'diabetes_melitus' => 'Terima kasih telah menyelesaikan skrining Diabetes Melitus. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila risiko tinggi untuk pemeriksaan gula darah.',
            'hipertensi' => 'Terima kasih telah menyelesaikan skrining Hipertensi. Berikut total skor dan klasifikasi risiko Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan bila risiko tinggi untuk pemeriksaan tekanan darah.',
        ];

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
            'question_prefix' => $questionPrefix,
            'warning_sign_ids' => $warningSignIds,
            'scoring_legend' => $scoringLegend,
            'screening_identity_id' => session('screening_identity_id'),
            'result' => [
                'title' => 'Skrining '.$diseaseConfig['label'].' Selesai',
                'message' => $resultMessages[$disease]
                    ?? 'Terima kasih telah menyelesaikan skrining '.$diseaseConfig['label'].'. Berikut ringkasan jawaban Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan jika keluhan memberat.',
            ],
        ];

        return view('detection.chat', compact('screening'));
    }
}
