@echo off
echo ========================================
echo Staff Attendance System - Full Setup
echo ========================================
echo.

REM Stop any existing containers
echo Stopping existing containers...
docker-compose down -v

REM Build and start services
echo.
echo Starting Docker Compose services...
docker-compose up -d

REM Wait for services to be ready
echo.
echo Waiting for services to start (30 seconds)...
timeout /t 30

REM Run Laravel migrations
echo.
echo Running Laravel migrations...
cd staff_attendance
php artisan migrate --force
php artisan route:clear
php artisan view:clear

REM Seed database if needed
echo.
echo Seeding database...
php artisan db:seed

cd ..

REM Display URLs
echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Service URLs:
echo - Laravel App: http://localhost:8000
echo - Grafana: http://localhost:3000 (admin/admin)
echo - Prometheus: http://localhost:9090
echo - phpMyAdmin: http://localhost:8081 (root/root)
echo - Metrics: http://localhost:8000/metrics
echo.
echo Wait 1-2 minutes for Prometheus to start scraping metrics.
echo Dashboard will auto-refresh every 10 seconds.
echo.
pause
