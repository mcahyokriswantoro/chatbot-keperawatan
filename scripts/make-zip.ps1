Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$src = "c:\Users\Laptopku\Documents\GitHub\chatbot-keperawatan"
$dest = Join-Path $src "deploy\chatbot-keperawatan-full-15juli2026-v21.zip"

if (Test-Path $dest) { Remove-Item $dest -Force }

Write-Host "Creating ZIP with forward slashes (Linux-compatible)..."

# Exclude patterns
$excludeDirs = @('.git', 'node_modules', '_zip_temp', 'deploy')
$excludeFiles = @('.env', 'nersia-health-deploy.zip', 'tmp-search.html')
$excludePublicFiles = @('.user.ini', 'hot')

$zip = [System.IO.Compression.ZipFile]::Open($dest, [System.IO.Compression.ZipArchiveMode]::Create)

$count = 0

function Add-FileToZip($filePath, $entryName, $zip) {
    # Convert backslashes to forward slashes for Linux compatibility
    $entryName = $entryName.Replace('\', '/')
    
    $compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $filePath, $entryName, $compressionLevel) | Out-Null
}

# Get all files recursively
$allFiles = Get-ChildItem $src -Recurse -File -Force

foreach ($file in $allFiles) {
    $relativePath = $file.FullName.Substring($src.Length + 1)
    $relativeDir = $relativePath.Split('\')[0]
    
    # Skip excluded directories
    if ($excludeDirs -contains $relativeDir) { continue }
    
    # Skip excluded files at root
    if ($excludeFiles -contains $file.Name -and $file.Directory.FullName -eq $src) { continue }
    
    # Skip .user.ini and hot in public/
    if ($relativePath.StartsWith('public\')) {
        $publicFile = $relativePath.Substring(7) # remove 'public\'
        if ($excludePublicFiles -contains $publicFile) { continue }
    }
    
    # Skip ZIP files inside deploy folder (but we already skip deploy/)
    # Skip the output file itself
    if ($file.FullName -eq $dest) { continue }
    
    Add-FileToZip $file.FullName $relativePath $zip
    $count++
    
    if ($count % 500 -eq 0) {
        Write-Host "  $count files added..."
    }
}

$zip.Dispose()

$fileInfo = Get-Item $dest
$sizeMB = [math]::Round($fileInfo.Length / 1MB, 1)
Write-Host ""
Write-Host "ZIP created: $dest"
Write-Host "Total files: $count"
Write-Host "Size: $sizeMB MB"
