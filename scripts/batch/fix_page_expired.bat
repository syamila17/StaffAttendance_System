@echo off
REM Fix "Page Expired" Error - Complete Setup Script
REM This script performs all necessary steps to fix the CSRF/session issue

cd /d "%~dp0..\staff_attendance" || (
    echo Error: Could not navigate to staff_attendance directory
    pause
    exit /b 1
)

echo.
echo ========================================
echo  Fixing "Page Expired" Error
echo ========================================
echo.

echo [1/7] Clearing cache...
php artisan cache:clear
if errorlevel 1 goto error

echo [2/7] Clearing config cache...
php artisan config:cache
if errorlevel 1 goto error

echo [3/7] Clearing view cache...
php artisan view:clear
if errorlevel 1 goto error

echo [4/7] Clearing session storage...
php artisan session:clear
if errorlevel 1 goto error

echo [5/7] Running database migrations...
php artisan migrate --force
if errorlevel 1 goto error

echo [6/7] Refreshing optimized autoloader...
php artisan optimize
if errorlevel 1 goto error

echo [7/7] Done!
echo.
echo ========================================
echo  Configuration Changes Made:
echo ========================================
echo.
echo - Changed SESSION_DRIVER from 'file' to 'database'
echo - Created sessions table migration
echo - Ensured CSRF token configuration
echo - Cleared all caches
echo.
echo ========================================
echo  Next Steps:
echo ========================================
echo.
echo 1. RESTART YOUR LARAVEL SERVER
echo 2. Clear browser cookies for localhost:8000
echo 3. Clear browser cache
echo 4. Close browser and reopen
echo 5. Try the staff management page again
echo.
echo ========================================
echo.
pause
exit /b 0

:error
echo.
echo ERROR: Command failed. Check the output above.
echo.
pause
exit /b 1
