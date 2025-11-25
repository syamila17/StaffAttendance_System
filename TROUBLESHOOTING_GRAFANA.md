# Grafana + Prometheus Troubleshooting Guide

## Common Issues and Solutions

### 1. Metrics Not Appearing in Grafana

**Problem**: Dashboard shows "No data" for all panels

**Solutions**:
```bash
# Check if Laravel metrics endpoint is working
curl http://localhost:8000/metrics

# Check Prometheus targets status
curl http://localhost:9090/api/v1/targets

# View Prometheus logs
docker logs prometheus_staff

# Check if Prometheus can reach Laravel app
docker exec prometheus_staff curl http://host.docker.internal:8000/metrics
```

**Fix**: 
- Ensure Laravel app is running on port 8000
- Check if metrics endpoint returns data
- Wait 1-2 minutes for Prometheus to scrape first data

---

### 2. Prometheus Can't Connect to Laravel App

**Problem**: "Unreachable" or "Down" in Prometheus targets

**Solution**:
```yaml
# In prometheus.yml, ensure correct target:
scrape_configs:
  - job_name: 'laravel-app'
    static_configs:
      - targets: ['host.docker.internal:8000']  # For Windows Docker
```

For different systems:
- **Windows**: `host.docker.internal:8000`
- **Linux**: `localhost:8000` or `172.17.0.1:8000`
- **macOS**: `host.docker.internal:8000`

---

### 3. Grafana Can't Connect to Prometheus

**Problem**: "Prometheus: Bad Gateway" in datasource test

**Solution**:
1. Go to http://localhost:3000/datasources
2. Edit "Prometheus" datasource
3. Change URL to: `http://prometheus:9090` (internal Docker network)
4. Click "Save & Test"

---

### 4. Port Already in Use

**Problem**: 
```
Error response from daemon: Ports are not available
```

**Solution**:
```bash
# Find what's using the port (e.g., 3000 for Grafana)
netstat -ano | findstr :3000

# Kill the process
taskkill /PID <PID> /F

# Or change port in docker-compose.yml
# "3001:3000"  # Maps localhost:3001 to container:3000
```

---

### 5. Dashboard Not Auto-Refreshing

**Problem**: Manual refresh needed, not auto-updating every 10 seconds

**Solution**:
1. Click dashboard title → "Settings"
2. Find "Refresh" dropdown (top right)
3. Select "10s" or set custom interval
4. Panels should now auto-refresh

---

### 6. No Data in Prometheus Database

**Problem**: Prometheus shows 0 series/samples

**Solution**:
```bash
# Check if Laravel app has attendance data
docker exec mysql_staff mysql -u root -proot staff_attendance -e "SELECT COUNT(*) FROM attendance;"

# Seed with test data if needed
docker-compose exec app php artisan db:seed

# Check metrics endpoint returns non-zero values
curl http://localhost:8000/metrics | grep attendance_
```

---

### 7. Grafana Dashboard Not Loading

**Problem**: Dashboard shows "Dashboard not found"

**Solution**:
```bash
# Check provisioning files
docker exec grafana_staff ls -la /etc/grafana/provisioning/dashboards/

# Check Grafana logs
docker logs grafana_staff

# Restart Grafana
docker restart grafana_staff

# Wait 30 seconds then refresh browser
```

---

### 8. High CPU/Memory Usage

**Problem**: Docker containers using too many resources

**Solution**:
```bash
# Check resource usage
docker stats

# Reduce Prometheus retention period (in docker-compose.yml)
# '--storage.tsdb.retention.time=7d'  # Reduce from 30d

# Restart services
docker-compose restart
```

---

### 9. Permission Denied Errors

**Problem**: 
```
permission denied while trying to connect to Docker daemon
```

**Solution**:
- Run Command Prompt as Administrator
- Or add user to docker group (Linux/Mac)

---

### 10. Metrics Endpoint Returning Empty

**Problem**: 
```
curl http://localhost:8000/metrics
# Returns empty or error
```

**Solution**:
```bash
# Check Laravel app is running
curl http://localhost:8000/login

# Check metrics controller exists
docker exec app php artisan route:list | grep metrics

# Check database has data
docker exec mysql_staff mysql -u root -proot staff_attendance -e "SELECT * FROM attendance LIMIT 5;"

# View Laravel logs
docker logs -f app
```

---

## Debug Commands

### View All Service Logs
```bash
# Follow all logs
docker-compose logs -f

# Follow specific service
docker-compose logs -f grafana
docker-compose logs -f prometheus
docker-compose logs -f mysql
```

### Check Service Health
```bash
# List running containers
docker-compose ps

# Inspect specific container
docker inspect grafana_staff

# Test connectivity between containers
docker exec grafana_staff curl http://prometheus:9090/api/v1/query?query=up
```

### Query Prometheus Directly
```bash
# HTTP API query
curl 'http://localhost:9090/api/v1/query?query=attendance_present_today'

# Query range
curl 'http://localhost:9090/api/v1/query_range?query=attendance_present_today&start=1700000000&end=1700100000&step=10s'
```

### Check File Permissions
```bash
# Inside Docker containers
docker exec prometheus_staff ls -la /etc/prometheus/
docker exec grafana_staff ls -la /etc/grafana/provisioning/
```

---

## Performance Optimization

### Increase Metric Retention
```yaml
# docker-compose.yml
prometheus:
  command:
    - '--storage.tsdb.retention.time=90d'  # Increase from 30d
```

### Optimize Scrape Interval
```yaml
# For less frequent updates (save resources)
scrape_configs:
  - job_name: 'laravel-app'
    scrape_interval: 30s  # Increase from 10s
```

### Enable Compression
```yaml
prometheus:
  command:
    - '--storage.tsdb.retention.time=30d'
    - '--query.timeout=2m'
    - '--query.max-concurrent=4'
```

---

## Testing Checklist

- [ ] Laravel app running: `curl http://localhost:8000/login`
- [ ] Metrics endpoint working: `curl http://localhost:8000/metrics`
- [ ] Prometheus running: `curl http://localhost:9090`
- [ ] Grafana running: `curl http://localhost:3000/login`
- [ ] Prometheus scraping: Check http://localhost:9090/targets
- [ ] Datasource connected: Check Grafana datasources
- [ ] Dashboard loading: Check http://localhost:3000/dashboards
- [ ] Data appearing: Check dashboard panels for values
- [ ] Auto-refresh working: Watch dashboard update every 10s

---

## Reset Everything

If all else fails:

```bash
# Stop all services
docker-compose down -v

# Remove all volumes (WARNING: deletes data!)
docker volume prune

# Restart fresh
docker-compose up -d

# Wait 30 seconds for initialization
timeout /t 30

# Seed database
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed
```

---

## Support Resources

- [Prometheus Documentation](https://prometheus.io/docs/)
- [Grafana Documentation](https://grafana.com/docs/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs/)
