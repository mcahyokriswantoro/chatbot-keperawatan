<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DetectionController extends Controller
{
    public function index(): View
    {
        return view('detection.menu', [
            'diseases' => config('diseases'),
        ]);
    }

    public function show(string $disease): View
    {
        $diseases = config('diseases');

        abort_unless(isset($diseases[$disease]), 404);

        $diseaseConfig = $diseases[$disease];

        $screening = [
            'bot_name' => config('screening.bot_name'),
            'disease' => $disease,
            'disease_label' => $diseaseConfig['label'],
            'welcome' => $diseaseConfig['welcome'],
            'start_options' => config('screening.start_options'),
            'questions' => $diseaseConfig['questions'],
            'result' => [
                'title' => 'Skrining '.$diseaseConfig['label'].' Selesai',
                'message' => 'Terima kasih telah menyelesaikan skrining '.$diseaseConfig['label'].'. Berikut ringkasan jawaban Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan jika keluhan memberat.',
            ],
        ];

        return view('detection.chat', compact('screening'));
    }
}
