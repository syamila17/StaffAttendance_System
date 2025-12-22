@echo off
REM Clear Laravel Cache and Sessions - Staff Attendance System
REM This script clears all caches to apply configuration changes

cd /d "%~dp0..\staff_attendance" || (
    echo Error: Could not navigate to staff_attendance directory
    pause
    exit /b 1
)

echo.
echo ========================================
echo  Laravel Cache Clearing Script
echo ========================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear
if errorlevel 1 echo Warning: Cache clear failed

echo [2/5] Clearing configuration cache...
php artisan config:cache
if errorlevel 1 echo Warning: Config cache failed

echo [3/5] Clearing view cache...
php artisan view:clear
if errorlevel 1 echo Warning: View clear failed

echo [4/5] Clearing session storage...
php artisan session:clear
if errorlevel 1 echo Warning: Session clear failed

echo [5/5] Optimizing autoloader...
php artisan optimize
if errorlevel 1 echo Warning: Optimize failed

echo.
echo ========================================
echo  Cache clearing complete!
echo ========================================
echo.
echo Next steps:
echo 1. Restart your Laravel development server
echo 2. Clear browser cache and cookies
echo 3. Try accessing the staff management page again
echo.
pause
