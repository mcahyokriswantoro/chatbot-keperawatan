Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$src  = "c:\Users\Laptopku\Documents\GitHub\chatbot-keperawatan"
$date = Get-Date -Format "ddMMMyyyy"
$dest = Join-Path $src "deploy\chatbot-keperawatan-deploy-$date.zip"

if (Test-Path $dest) { Remove-Item $dest -Force }

Write-Host "Creating deploy ZIP: $dest"
Write-Host "Excluding: .git, node_modules, vendor, .env, storage/logs, *.zip, tmp files..."
Write-Host ""

# Directories to skip entirely
$excludeDirs = @(
    '.git',
    'node_modules',
    'deploy',
    'scripts'
)

# Top-level files to skip
$excludeRootFiles = @(
    '.env',
    'nersia-health-deploy.zip',
    'tmp-search.html',
    'temp_test.php'
)

# Specific sub-paths to skip (relative from $src, using backslash)
$excludeSubPaths = @(
    'storage\logs',
    'storage\framework\cache',
    'storage\framework\sessions',
    'storage\framework\views',
    'bootstrap\cache',
    'public\.user.ini',
    'public\hot',
    'public\debug-order.php',
    'public\dump-v21-data.php',
    'public\import-v21-data.php',
    'public\v21_data.json'
)

# Extensions to skip
$excludeExtensions = @('.log', '.zip')

$zip   = [System.IO.Compression.ZipFile]::Open($dest, [System.IO.Compression.ZipArchiveMode]::Create)
$count = 0

function Add-FileToZip($filePath, $entryName, $zip) {
    $entryName = $entryName.Replace('\', '/')
    $level = [System.IO.Compression.CompressionLevel]::Optimal
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $filePath, $entryName, $level) | Out-Null
}

$allFiles = Get-ChildItem $src -Recurse -File -Force

foreach ($file in $allFiles) {
    $rel     = $file.FullName.Substring($src.Length + 1)
    $topDir  = $rel.Split('\')[0]

    # Skip excluded top-level dirs
    if ($excludeDirs -contains $topDir) { continue }

    # Skip excluded root-level files
    if ($excludeRootFiles -contains $file.Name -and $file.Directory.FullName -eq $src) { continue }

    # Skip excluded sub-paths (prefix match)
    $skipSubPath = $false
    foreach ($sub in $excludeSubPaths) {
        if ($rel -eq $sub -or $rel.StartsWith($sub + '\')) {
            $skipSubPath = $true
            break
        }
    }
    if ($skipSubPath) { continue }

    # Skip excluded extensions
    if ($excludeExtensions -contains $file.Extension.ToLower()) { continue }

    # Skip the output file itself
    if ($file.FullName -eq $dest) { continue }

    Add-FileToZip $file.FullName $rel $zip
    $count++

    if ($count % 500 -eq 0) {
        Write-Host "  $count files added..."
    }
}

$zip.Dispose()

$info   = Get-Item $dest
$sizeMB = [math]::Round($info.Length / 1MB, 1)

Write-Host ""
Write-Host "Done!" -ForegroundColor Green
Write-Host "ZIP : $dest"
Write-Host "Files: $count"
Write-Host "Size : $sizeMB MB"
