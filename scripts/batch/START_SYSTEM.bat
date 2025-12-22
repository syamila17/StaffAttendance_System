@echo off
REM ==========================================
REM STAFF ATTENDANCE SYSTEM - COMPLETE FIX
REM ==========================================

echo.
echo [*] Stopping any running PHP processes...
taskkill /F /IM php.exe >nul 2>&1

timeout /t 2 /nobreak

cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

echo.
echo [*] Clearing Laravel caches...
php artisan cache:clear --force >nul 2>&1
php artisan route:clear --force >nul 2>&1
php artisan view:clear --force >nul 2>&1
php artisan config:clear --force >nul 2>&1

timeout /t 1 /nobreak

echo.
echo [*] Verifying database connection...
php artisan tinker --execute "dd(\App\Models\Staff::count());" 2>nul || echo [!] Warning: Database might be down

echo.
echo ==========================================
echo STARTING LARAVEL SERVER
echo ==========================================
echo.
echo URL: http://localhost:8000/login
echo.
echo KEEP THIS WINDOW OPEN
echo Press Ctrl+C to stop
echo.
timeout /t 2 /nobreak

php artisan serve --host=0.0.0.0 --port=8000

pause
