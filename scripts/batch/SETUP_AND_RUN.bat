@echo off
REM Complete system setup with database migrations
title Staff Attendance System - Setup
cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

cls
echo.
echo ===============================================
echo  STAFF ATTENDANCE SYSTEM - COMPLETE SETUP
echo ===============================================
echo.

echo [1/3] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Migrations failed!
    pause
    exit /b 1
)
echo [OK] Migrations completed

echo.
echo [2/3] Seeding test data...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo [ERROR] Seeding failed!
    pause
    exit /b 1
)
echo [OK] Database seeded

echo.
echo [3/3] Clearing caches...
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo [OK] Caches cleared

echo.
echo ===============================================
echo    SETUP COMPLETE - STARTING SERVER
echo ===============================================
echo.
echo URL: http://localhost:8000/login
echo.
echo Credentials:
echo   Staff: ahmad@utm.edu.my / password123
echo   Admin: admin@utm.edu.my / admin123
echo.
echo Press Ctrl+C to stop
echo.

php artisan serve --host=127.0.0.1 --port=8000

pause
