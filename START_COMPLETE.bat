@echo off
REM Complete system startup with verification
title Staff Attendance System - Complete Startup
cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

cls
echo.
echo ===============================================
echo    STAFF ATTENDANCE SYSTEM - STARTUP
echo ===============================================
echo.

echo [1/4] Checking PHP installation...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP not found! Install PHP first.
    pause
    exit /b 1
)
echo [OK] PHP found

echo.
echo [2/4] Checking Laravel installation...
if not exist "vendor\laravel\framework" (
    echo [ERROR] Laravel not found! Run: composer install
    pause
    exit /b 1
)
echo [OK] Laravel found

echo.
echo [3/4] Checking database...
php artisan tinker --execute "dd(\App\Models\Staff::count());" >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Database might not be running
    echo Run: docker-compose up -d
) else (
    echo [OK] Database connected
)

echo.
echo [4/4] Starting Laravel server...
echo.
echo ===============================================
echo URL: http://localhost:8000/login
echo Login: ahmad@utm.edu.my / password123
echo ===============================================
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve --host=127.0.0.1 --port=8000

pause
