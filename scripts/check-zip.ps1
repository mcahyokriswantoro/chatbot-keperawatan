Add-Type -AssemblyName System.IO.Compression.FileSystem
$zipPath = "c:\Users\Laptopku\Documents\GitHub\chatbot-keperawatan\deploy\chatbot-keperawatan-full-15juli2026-v21.zip"
$zip = [System.IO.Compression.ZipFile]::OpenRead($zipPath)

# Check for key files
$keyFiles = @("public\version.txt", "artisan", "public\index.php", "routes\web.php", ".env")
foreach ($kf in $keyFiles) {
    $found = $zip.Entries | Where-Object { $_.FullName -eq $kf -or $_.FullName -eq $kf.Replace('\','/') } | Select-Object -First 1
    if ($found) {
        Write-Host "FOUND: $($found.FullName) ($($found.Length) bytes)"
    } else {
        Write-Host "MISSING: $kf"
    }
}

# Count total entries
$total = $zip.Entries.Count
Write-Host "`nTotal entries in ZIP: $total"
$zip.Dispose()
