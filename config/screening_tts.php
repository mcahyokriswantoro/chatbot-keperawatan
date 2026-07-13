<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Suara presenter (gaya TikTok / CapCut edukasi medis)
    |--------------------------------------------------------------------------
    |
    | Konten medis Indonesia umumnya memakai suara perempuan natural (GadisNeural)
    | dengan tempo sedikit lebih pelan. Set false untuk ikut gender profil user.
    |
    */
    'driver' => env('TTS_DRIVER', 'auto'), // auto | php | node — hosting shared pakai php (tanpa Node.js)

    /*
    | true = suara langsung dari peramban (Web Speech API), tanpa tunggu API server.
    | false = sintesis neural Edge TTS via server (lebih natural, butuh jaringan).
    */
    'client_only' => env('TTS_CLIENT_ONLY', true),

    'use_presenter_voice' => env('TTS_USE_PRESENTER_VOICE', true),

    'voices' => [
        'presenter' => env('TTS_PRESENTER_VOICE', 'id-ID-GadisNeural'),
        'female' => env('TTS_FEMALE_VOICE', 'id-ID-GadisNeural'),
        'male' => env('TTS_MALE_VOICE', 'id-ID-ArdiNeural'),
    ],

    /*
    | Tempo & nada — sedikit lebih pelan & jelas seperti video edukasi medis.
    */
    'prosody' => [
        'rate' => env('TTS_RATE', '-2%'),
        'pitch' => env('TTS_PITCH', '-2Hz'),
        'volume' => env('TTS_VOLUME', '+0%'),
    ],

    'node_binary' => env('NODE_BINARY', PHP_OS_FAMILY === 'Windows'
        ? 'C:\\Program Files\\nodejs\\node.exe'
        : 'node'),

    'timeout_seconds' => (int) env('TTS_TIMEOUT', 180),

];
