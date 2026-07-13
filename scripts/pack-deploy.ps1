$ErrorActionPreference = 'Stop'

$root = Resolve-Path (Join-Path $PSScriptRoot '..')

$zipName = 'chatbot-keperawatan-full-04juli2026-v20.zip'
$staging = Join-Path $env:TEMP ("chatbot-deploy-v20-" + (Get-Date -Format 'yyyyMMddHHmmss'))
$zipPath = Join-Path $root "deploy\$zipName"
$guideName = 'CARA-UPDATE-04-JULI-2026-V20-FULL.txt'

Write-Host "Root: $root"
Write-Host "Staging: $staging"

if (Test-Path $staging) {
    Remove-Item $staging -Recurse -Force -ErrorAction SilentlyContinue
}
New-Item -ItemType Directory -Path $staging -Force | Out-Null

function Copy-Tree($name) {
    $src = Join-Path $root $name
    $dst = Join-Path $staging $name
    if (-not (Test-Path $src)) {
        throw "Missing folder: $src"
    }
    robocopy $src $dst /E /NFL /NDL /NJH /NJS /nc /ns /np | Out-Null
    if ($LASTEXITCODE -ge 8) {
        throw "robocopy failed for $name (exit $LASTEXITCODE)"
    }
}

foreach ($dir in @('app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'scripts', 'vendor')) {
    Write-Host "Copy $dir..."
    Copy-Tree $dir
}

Write-Host 'Copy public (exclude .user.ini)...'
robocopy (Join-Path $root 'public') (Join-Path $staging 'public') /E /XJ /NFL /NDL /NJH /NJS /nc /ns /np /XF hot .user.ini | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy public failed" }

Write-Host 'Copy public_html mirror...'
robocopy (Join-Path $root 'public') (Join-Path $staging 'public_html') /E /XJ /NFL /NDL /NJH /NJS /nc /ns /np /XF hot .user.ini | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy public_html failed" }

Write-Host 'Copy storage skeleton...'
robocopy (Join-Path $root 'storage') (Join-Path $staging 'storage') /E /XJ /NFL /NDL /NJH /NJS /nc /ns /np /XD logs 'framework\cache\data' 'framework\sessions' 'framework\views' 'app\public\article-videos' 'app\public\article-covers' 'app\public\consultation-providers' 'app\public\consultation-payment-proofs' | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy storage failed" }
foreach ($empty in @('storage\logs', 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views', 'storage\app\public\article-videos', 'storage\app\public\article-covers', 'storage\app\public\consultation-providers', 'storage\app\public\consultation-payment-proofs')) {
    New-Item -ItemType Directory -Force -Path (Join-Path $staging $empty) | Out-Null
}

Write-Host 'Copy deploy helpers...'
New-Item -ItemType Directory -Force -Path (Join-Path $staging 'deploy') | Out-Null
Get-ChildItem (Join-Path $root 'deploy') -File | Where-Object { $_.Extension -ne '.zip' } | ForEach-Object {
    Copy-Item -LiteralPath $_.FullName -Destination (Join-Path $staging "deploy\$($_.Name)") -Force
}

foreach ($file in @('artisan', 'composer.json', 'composer.lock', 'package.json', 'package-lock.json')) {
    Copy-Item (Join-Path $root $file) (Join-Path $staging $file) -Force
}

$guide = Join-Path $root "deploy\$guideName"
if (Test-Path $guide) {
    Copy-Item $guide (Join-Path $staging $guideName) -Force
}

Write-Host 'Creating ZIP...'
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Push-Location $staging
try {
    & tar.exe -a -cf $zipPath .
    if ($LASTEXITCODE -ne 0) {
        Write-Host 'tar failed, trying Compress-Archive...'
        Pop-Location
        Compress-Archive -Path (Join-Path $staging '*') -DestinationPath $zipPath -CompressionLevel Optimal -Force
        Push-Location $staging
    }
}
finally {
    Pop-Location
}

Remove-Item $staging -Recurse -Force -ErrorAction SilentlyContinue

$sizeMB = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "OK: $zipPath ($sizeMB MB)"
