@echo off
cls
echo ========================================
echo Grafana + Prometheus Quick Start
echo ========================================
echo.

echo Checking if containers are running...
docker ps | findstr "grafana_staff" >nul
if errorlevel 1 (
    echo Containers not running. Starting Docker Compose...
    docker-compose up -d
    echo.
    echo Waiting 30 seconds for services to initialize...
    timeout /t 30
) else (
    echo Containers already running.
)

echo.
echo ========================================
echo Service Status
echo ========================================
docker ps --filter "name=_staff" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo.
echo ========================================
echo Access URLs
echo ========================================
echo.
echo [1] Grafana Dashboard
echo     URL: http://localhost:3000
echo     User: admin
echo     Pass: admin
echo     Dashboard: "Staff Attendance Statistics"
echo.
echo [2] Prometheus (Metrics DB)
echo     URL: http://localhost:9090
echo     Status: Graph menu to query metrics
echo.
echo [3] Metrics Endpoint
echo     URL: http://localhost:8000/metrics
echo     Format: Prometheus text format
echo.
echo [4] Laravel App
echo     URL: http://localhost:8000
echo.
echo [5] phpMyAdmin
echo     URL: http://localhost:8081
echo     User: root
echo     Pass: root
echo.
echo ========================================
echo Important Notes
echo ========================================
echo.
echo - Dashboard auto-refreshes every 10 seconds
echo - Prometheus scrapes metrics every 10 seconds
echo - Wait 1-2 minutes for first data to appear
echo - Metrics calculated in real-time from database
echo.
echo To stop services:
echo   docker-compose down
echo.
echo To view logs:
echo   docker logs grafana_staff
echo   docker logs prometheus_staff
echo.
echo To restart services:
echo   docker-compose restart
echo.
pause
