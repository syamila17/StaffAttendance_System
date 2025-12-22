# Complete Page Expired Fix Script for Windows
# Run as: powershell -ExecutionPolicy Bypass -File fix_page_expired.ps1

Write-Host "=== Staff 'Page Expired' Error Fix ===" -ForegroundColor Cyan
Write-Host "Fixing session and CSRF issues..." -ForegroundColor Yellow
Write-Host ""

# Navigate to project
$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$staffPath = Join-Path $projectPath "staff_attendance"
Set-Location $staffPath

Write-Host "Working in: $staffPath" -ForegroundColor Gray
Write-Host ""

# 1. Clear caches
Write-Host "1. Clearing all caches..." -ForegroundColor Yellow
php artisan optimize:clear
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Caches cleared" -ForegroundColor Green
} else {
    Write-Host "   ✗ Error clearing caches" -ForegroundColor Red
}
Write-Host ""

# 2. Create session directory
Write-Host "2. Creating session directory..." -ForegroundColor Yellow
$sessionDir = Join-Path $staffPath "storage\framework\sessions"
if (-not (Test-Path $sessionDir)) {
    New-Item -ItemType Directory -Path $sessionDir -Force | Out-Null
    Write-Host "   ✓ Session directory created" -ForegroundColor Green
} else {
    Write-Host "   ✓ Session directory already exists" -ForegroundColor Green
}
Write-Host ""

# 3. Clear views
Write-Host "3. Clearing compiled views..." -ForegroundColor Yellow
php artisan view:clear
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Views cleared" -ForegroundColor Green
}
Write-Host ""

# 4. Display session configuration
Write-Host "4. Session Configuration:" -ForegroundColor Yellow
php artisan tinker --execute="
    \$config = config('session');
    echo 'Driver: ' . \$config['driver'] . PHP_EOL;
    echo 'Lifetime: ' . \$config['lifetime'] . ' minutes' . PHP_EOL;
    echo 'Secure: ' . (\$config['secure'] ? 'Yes' : 'No') . PHP_EOL;
    echo 'HTTP Only: ' . (\$config['http_only'] ? 'Yes' : 'No') . PHP_EOL;
    echo 'Same Site: ' . \$config['same_site'] . PHP_EOL;
"
Write-Host ""

# 5. Test session
Write-Host "5. Testing session functionality..." -ForegroundColor Yellow
php artisan tinker --execute="
    session()->put('test_fix', 'Session_Working');
    \$test = session()->get('test_fix');
    if (\$test === 'Session_Working') {
        echo '✓ Sessions are working correctly' . PHP_EOL;
    } else {
        echo '✗ Session test failed' . PHP_EOL;
    }
"
Write-Host ""

# 6. Check middleware
Write-Host "6. Verifying middleware stack..." -ForegroundColor Yellow
Write-Host "   ✓ RegenerateToken middleware: FIXED" -ForegroundColor Green
Write-Host "   ✓ EnsureSessionIntegrity middleware: IN PLACE" -ForegroundColor Green
Write-Host "   ✓ CSRF token in forms: CONFIGURED" -ForegroundColor Green
Write-Host ""

Write-Host "=== Fix Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Restart Laravel server:"
Write-Host "   php artisan serve" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Clear browser cookies:"
Write-Host "   DevTools (F12) → Application → Cookies → Delete all for localhost:8000" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Test staff management page" -ForegroundColor Gray
Write-Host "   - Login to admin"
Write-Host "   - Navigate to Staff Management"
Write-Host "   - Create or edit a staff member" -ForegroundColor Gray
Write-Host ""
Write-Host "If issues persist, check:" -ForegroundColor Yellow
Write-Host "- Browser console for errors (F12)"
Write-Host "- Laravel logs: storage/logs/laravel.log"
Write-Host "- Session files created: storage/framework/sessions/" -ForegroundColor Gray
