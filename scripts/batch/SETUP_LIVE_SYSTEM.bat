@echo off
REM ============================================
REM SISTEMA KEHADIRAN UTM - Live Setup
REM ============================================

echo.
echo ====================================
echo Sistema Kehadiran UTM - Live Setup
echo ====================================
echo.

REM Step 1: Navigate to project
cd /d "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"
if errorlevel 1 (
    echo ERROR: Could not navigate to project directory
    pause
    exit /b 1
)

echo [Step 1/4] Directory set: %cd%
echo.

REM Step 2: Clear config cache
echo [Step 2/4] Clearing configuration cache...
php artisan config:cache
if errorlevel 1 (
    echo ERROR: Failed to cache config
    pause
    exit /b 1
)
php artisan route:cache
echo Configuration cached successfully!
echo.

REM Step 3: Check sessions directory
echo [Step 3/4] Verifying sessions directory...
if not exist "storage\framework\sessions" (
    echo Creating sessions directory...
    mkdir storage\framework\sessions
)
echo Sessions directory ready!
echo.

REM Step 4: Display URLs
echo [Step 4/4] Setup Complete!
echo.
echo ====================================
echo ACCESS URLS
echo ====================================
echo.
echo Staff Portal:  http://staff.sistemkehadiranUTM.local:8000
echo Admin Portal:  http://admin.sistemkehadiranUTM.local:8000
echo Fallback:      http://localhost:8000
echo.
echo ====================================
echo TO START THE SERVER:
echo ====================================
echo.
echo php artisan serve
echo.
echo Press any key to open setup guide...
pause

REM Open documentation
start "" "C:\Users\syami\Desktop\StaffAttendance_System\documentation\MULTI_USER_LIVE_SETUP.md"
