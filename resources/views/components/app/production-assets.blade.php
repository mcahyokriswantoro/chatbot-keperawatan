@php
    $manifestPath = public_path('build/manifest.json');
    $cssFile = null;
    $jsFile = null;
    $siteCssPath = public_path('css/site.css');
    $siteCssVersion = is_file($siteCssPath) ? (string) filemtime($siteCssPath) : '1';
    $mobileCoreVersion = is_file(public_path('css/mobile-core.css')) ? (string) filemtime(public_path('css/mobile-core.css')) : '1';

    if (is_file($manifestPath)) {
        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    }
@endphp

{{-- Path relatif /css/... agar tidak tergantung APP_URL di config cache --}}
<link rel="stylesheet" href="/css/mobile-core.css?v={{ $mobileCoreVersion }}">

@if (is_file($siteCssPath))
    <link rel="stylesheet" href="/css/site.css?v={{ $siteCssVersion }}">
@elseif ($cssFile && is_file(public_path('build/'.$cssFile)))
    <link rel="stylesheet" href="/build/{{ $cssFile }}?v={{ filemtime(public_path('build/'.$cssFile)) }}">
@endif

@if ($jsFile && is_file(public_path('build/'.$jsFile)))
    <script type="module" src="/build/{{ $jsFile }}?v={{ filemtime(public_path('build/'.$jsFile)) }}"></script>
@endif
