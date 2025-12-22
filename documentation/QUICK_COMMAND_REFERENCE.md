# Quick Command Reference - Grafana + Prometheus

## Essential Commands

### Start/Stop Services
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Stop and remove volumes (WARNING: deletes data!)
docker-compose down -v

# Restart all services
docker-compose restart

# Restart specific service
docker-compose restart grafana
docker-compose restart prometheus
```

### View Logs
```bash
# All service logs
docker-compose logs

# Follow logs (real-time)
docker-compose logs -f

# Follow specific service
docker-compose logs -f grafana
docker-compose logs -f prometheus
docker-compose logs -f mysql_staff

# Last 50 lines
docker-compose logs --tail=50
```

### Service Status
```bash
# List running containers
docker-compose ps

# Check container stats (CPU, memory)
docker stats

# Inspect specific container
docker inspect grafana_staff

# View container IP
docker inspect -f '{{.NetworkSettings.IPAddress}}' grafana_staff
```

## Grafana Commands

### Access Grafana
```
URL: http://localhost:3000
Username: admin
Password: admin
```

### Reset Grafana Admin Password
```bash
# Connect to Grafana container
docker exec -it grafana_staff bash

# Inside container:
grafana-cli admin reset-admin-password newpassword

# Exit
exit
```

### View Grafana Provisioning
```bash
# Datasources
docker exec grafana_staff cat /etc/grafana/provisioning/datasources/prometheus.yml

# Dashboards
docker exec grafana_staff ls -la /etc/grafana/provisioning/dashboards/
```

## Prometheus Commands

### Access Prometheus
```
URL: http://localhost:9090
Graph: http://localhost:9090/graph
Status: http://localhost:9090/status
Targets: http://localhost:9090/targets
```

### Query Prometheus API
```bash
# Get all targets
curl http://localhost:9090/api/v1/targets

# Query specific metric
curl 'http://localhost:9090/api/v1/query?query=attendance_present_today'

# Range query (last 24 hours)
curl 'http://localhost:9090/api/v1/query_range?query=attendance_present_today&start=1700000000&end=1700100000&step=10s'

# List all metrics
curl 'http://localhost:9090/api/v1/label/__name__/values'
```

### Query Prometheus from Command Line
```bash
# Test connectivity
curl http://localhost:9090/-/healthy

# Get configuration
curl http://localhost:9090/api/v1/query?query=up

# Get targets status
curl http://localhost:9090/api/v1/targets | jq
```

## Laravel Metrics Commands

### Test Metrics Endpoint
```bash
# Raw metrics
curl http://localhost:8000/metrics

# Save to file
curl http://localhost:8000/metrics > metrics.txt

# Get specific metric
curl http://localhost:8000/metrics | grep attendance_present_today

# Count metrics
curl http://localhost:8000/metrics | wc -l
```

### View Laravel Routes
```bash
# List all routes
docker-compose exec app php artisan route:list

# Filter for metrics
docker-compose exec app php artisan route:list | grep metrics

# Show route details
docker-compose exec app php artisan route:show metrics
```

### Laravel Cache/Compilation
```bash
# Clear all caches
docker-compose exec app php artisan cache:clear

# Clear config cache
docker-compose exec app php artisan config:clear

# Clear view cache
docker-compose exec app php artisan view:clear

# Clear route cache
docker-compose exec app php artisan route:clear

# Optimize
docker-compose exec app php artisan optimize
```

## Database Commands

### MySQL/MariaDB Access
```bash
# Connect to MySQL
docker exec -it mysql_staff mysql -u root -proot

# Inside MySQL:
use staffAttend_data;
SELECT COUNT(*) FROM attendance;
SELECT DISTINCT status FROM attendance;
SELECT status, COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() GROUP BY status;
SELECT * FROM attendance WHERE attendance_date = CURDATE() LIMIT 5;
```

### Database Operations
```bash
# Run migrations
docker-compose exec app php artisan migrate --force

# Seed database
docker-compose exec app php artisan db:seed

# Reset database (DANGER!)
docker-compose exec app php artisan migrate:reset

# Migrate:fresh with seed
docker-compose exec app php artisan migrate:fresh --seed

# Check migrations status
docker-compose exec app php artisan migrate:status
```

### Backup/Restore
```bash
# Backup database
docker exec mysql_staff mysqldump -u root -proot staffAttend_data > backup.sql

# Restore database
docker exec -i mysql_staff mysql -u root -proot staffAttend_data < backup.sql
```

## Docker Troubleshooting

### Check Container Health
```bash
# Inspect container
docker inspect grafana_staff

# Get container IP
docker inspect grafana_staff | grep IPAddress

# Test network connectivity
docker exec prometheus_staff curl http://grafana:3000

# Test database connectivity
docker exec prometheus_staff curl http://host.docker.internal:8000/metrics
```

### Volume Management
```bash
# List volumes
docker volume ls

# Inspect volume
docker volume inspect StaffAttendance_System_grafana_data

# Prune unused volumes
docker volume prune

# Remove specific volume
docker volume rm StaffAttendance_System_grafana_data
```

### Network Management
```bash
# List networks
docker network ls

# Inspect network
docker network inspect StaffAttendance_System_staff-network

# Test network connectivity
docker exec grafana_staff ping prometheus
```

## Performance Monitoring

### Monitor Resource Usage
```bash
# Real-time stats
docker stats

# JSON output
docker stats --format="{{json .}}"

# Specific container
docker stats grafana_staff

# Historical stats
docker stats --no-stream
```

### Check Prometheus Storage
```bash
# Show storage usage
docker exec prometheus_staff du -sh /prometheus

# List database files
docker exec prometheus_staff ls -lah /prometheus

# Check inodes
docker exec prometheus_staff df -i /prometheus
```

## Configuration Updates

### Update Prometheus Config (without restart)
```bash
# Edit prometheus.yml
# Then reload:
curl -X POST http://localhost:9090/-/reload
```

### Update Grafana Settings
```bash
# Edit grafana.ini
# Restart service:
docker-compose restart grafana
```

### Update Docker Compose
```bash
# After editing docker-compose.yml:
docker-compose up -d  # Updates running services
```

## Batch File Commands

### Run Setup Scripts
```bash
# Full setup with migrations
.\SETUP_GRAFANA.bat

# Quick start
.\START_GRAFANA.bat

# Make scripts executable
# (Already .bat files, just run)
```

## Network Troubleshooting

### Test Connectivity Between Services
```bash
# Prometheus → Laravel App
docker exec prometheus_staff curl http://host.docker.internal:8000/metrics

# Grafana → Prometheus
docker exec grafana_staff curl http://prometheus:9090

# Laravel → MySQL
docker exec app php artisan tinker
# > DB::connection()->getPdo();
# Should return PDO connection
```

### Port Troubleshooting
```bash
# Windows - Find process using port
netstat -ano | findstr :3000

# Kill process
taskkill /PID <PID> /F

# Check if port is open
netstat -an | findstr 3000
```

## Useful PromQL Queries

### Run in Prometheus Graph (http://localhost:9090/graph)

```promql
# Current value
attendance_present_today

# Rate of change (per second)
rate(attendance_present_today[5m])

# Difference from yesterday
attendance_present_today offset 1d

# Multiple metrics
{job="laravel-app"}

# Filter by status
attendance_by_status{status="present"}

# Top 3 statuses
topk(3, attendance_by_status)

# Sum all
sum(attendance_by_status)

# Average over time
avg(attendance_present_today)

# Maximum value
max(attendance_present_today)

# Minimum value
min(attendance_present_today)
```

## Emergency Commands

### Full System Reset
```bash
# DANGER: This deletes everything
docker-compose down -v

# Remove images too
docker-compose down -v --remove-orphans --rmi all

# Clean start
docker-compose up -d

# Reinitialize
docker-compose exec app php artisan migrate:fresh --seed
```

### Clear All Docker
```bash
# WARNING: Removes all containers, volumes, images!

# Remove all containers
docker container prune -f

# Remove all volumes
docker volume prune -f

# Remove all unused images
docker image prune -f

# Total cleanup
docker system prune -af
```

### Quick Health Check Script
```bash
#!/bin/bash
echo "Checking services..."
echo "Grafana: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:3000/login)"
echo "Prometheus: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:9090)"
echo "Laravel: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/login)"
echo "Metrics: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/metrics)"
echo "MySQL: $(docker exec mysql_staff mysqladmin -u root -proot ping)"
```

## Save This As: Quick Reference

Print or bookmark this page for quick reference during setup and troubleshooting.

---

**Last Updated**: November 25, 2025
**Tested With**: Docker Desktop, Windows 10/11
**Platforms**: Windows, Mac, Linux
