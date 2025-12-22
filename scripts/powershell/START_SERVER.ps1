# Stop any existing Laravel servers
Write-Host "Stopping any existing Laravel servers..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

Start-Sleep -Seconds 1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Starting Laravel Development Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Navigate to project
Set-Location "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

Write-Host "Checking PHP installation..." -ForegroundColor Yellow
php -v

Write-Host ""
Write-Host "Clearing caches..." -ForegroundColor Yellow
php artisan cache:clear --force
php artisan route:clear --force
php artisan view:clear --force

Write-Host ""
Write-Host "Starting Laravel server on port 8000..." -ForegroundColor Green
Write-Host "Visit: http://localhost:8000/login" -ForegroundColor Green
Write-Host ""
Write-Host "⚠️  Keep this window open! Do not close it!" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

php artisan serve --host=127.0.0.1 --port=8000
