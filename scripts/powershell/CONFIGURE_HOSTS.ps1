# ============================================
# Sistema Kehadiran UTM - Configure Hosts File
# ============================================
# Run as Administrator in PowerShell

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$domains = @(
    "127.0.0.1       sistemkehadiranUTM.local",
    "127.0.0.1       staff.sistemkehadiranUTM.local",
    "127.0.0.1       admin.sistemkehadiranUTM.local"
)

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Sistema Kehadiran UTM - Hosts Configuration" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Please right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    pause
    exit 1
}

# Read current hosts file
Write-Host "[Step 1/3] Reading hosts file..." -ForegroundColor Yellow
$hostsContent = Get-Content $hostsPath

# Check if domains already exist
$domainExists = $false
foreach ($domain in $domains) {
    if ($hostsContent -contains $domain) {
        $domainExists = $true
        break
    }
}

if ($domainExists) {
    Write-Host "✓ Domains already configured in hosts file" -ForegroundColor Green
} else {
    Write-Host "[Step 2/3] Adding domains to hosts file..." -ForegroundColor Yellow
    
    # Add new domains
    $newContent = $hostsContent + "`r`n`r`n# Sistema Kehadiran UTM Domains`r`n"
    foreach ($domain in $domains) {
        $newContent += $domain + "`r`n"
    }
    
    # Write updated hosts file
    Set-Content -Path $hostsPath -Value $newContent
    Write-Host "✓ Domains added successfully" -ForegroundColor Green
}

Write-Host "[Step 3/3] Verifying configuration..." -ForegroundColor Yellow

# Verify domains
$hostsContent = Get-Content $hostsPath
$verified = 0
foreach ($domain in $domains) {
    if ($hostsContent -contains $domain) {
        Write-Host "✓ $domain" -ForegroundColor Green
        $verified++
    }
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "Configuration Complete!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Verified domains: $verified/3" -ForegroundColor Cyan
Write-Host ""
Write-Host "You can now access:" -ForegroundColor Yellow
Write-Host "  Staff Portal:  http://staff.sistemkehadiranUTM.local:8000" -ForegroundColor Cyan
Write-Host "  Admin Portal:  http://admin.sistemkehadiranUTM.local:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
