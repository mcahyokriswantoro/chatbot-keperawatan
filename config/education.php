<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas upload video (admin)
    |--------------------------------------------------------------------------
    |
    | Nilai dalam kilobyte (Laravel rule "max").
    | Default 122880 KB = 120 MB — sedikit di bawah post_max_size 128M
    | agar masih ada ruang untuk thumbnail + field form.
    |
    */
    'video_max_upload_kb' => (int) env('VIDEO_MAX_UPLOAD_KB', 122880),

];
