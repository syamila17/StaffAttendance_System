@echo off
echo ========================================
echo Starting Laravel Development Server
echo ========================================

cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

echo.
echo Checking PHP installation...
php -v

echo.
echo Starting Laravel server on port 8000...
echo Visit: http://localhost:8000/login
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve --host=127.0.0.1 --port=8000

pause
