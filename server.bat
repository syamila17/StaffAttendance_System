@echo off
title Staff Attendance System
cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"
cls
echo Starting Server...
echo.
php artisan serve --host=127.0.0.1 --port=8000
