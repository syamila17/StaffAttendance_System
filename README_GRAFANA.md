# Staff Attendance System - Grafana & Prometheus Integration

## 📊 Overview

This setup integrates **Grafana** and **Prometheus** with your Laravel Staff Attendance System to provide:

- ✅ Real-time attendance statistics
- ✅ Auto-refreshing dashboards (10-second intervals)
- ✅ Historical trend analysis (last 7 days)
- ✅ Professional visualization with charts and metrics
- ✅ Centralized monitoring of all attendance data

## 🚀 Quick Start

### Step 1: Start Services
```bash
# Navigate to project root
cd c:\Users\syami\Desktop\StaffAttendance_System

# Start all Docker containers
docker-compose up -d
```

### Step 2: Wait for Initialization
```
30 seconds - Docker containers initializing
1-2 minutes - Prometheus scraping first metrics
```

### Step 3: Access Grafana Dashboard
```
URL: http://localhost:3000
Username: admin
Password: admin
Dashboard: "Staff Attendance Statistics"
```

## 📈 Dashboards & Metrics

### Real-time Statistics Cards (Auto-refresh 10s)
- **Total Present Today** - Green badge with count
- **Total Absent Today** - Red badge with count
- **Total Late Today** - Yellow badge with count
- **Total On Leave Today** - Blue badge with count

### Visual Charts
1. **Pie Chart**: Attendance status breakdown (all 6 statuses)
2. **Line Chart**: 7-day trend showing present/absent/late patterns

### Available Metrics

```
attendance_present_today          # Staff present count
attendance_absent_today           # Staff absent count
attendance_late_today             # Staff late count
attendance_el_today               # Emergency leave count
attendance_leave_today            # On leave count
attendance_halfday_today          # Half day count
attendance_total_staff            # Total staff recorded today
attendance_by_status{status="X"}  # Breakdown by status
attendance_daily_*                # Historical data (7 days)
```

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│         Your Browser                     │
├─────────────────────────────────────────┤
│  http://localhost:3000 (Grafana)        │
│  - Staff Attendance Statistics          │
│  - Auto-refresh every 10 seconds        │
└──────────────────┬──────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
   ┌────▼─────┐          ┌────▼────────┐
   │Prometheus│          │ Grafana     │
   │(9090)    │◄────────►│ (3000)      │
   └────▲─────┘          └─────────────┘
        │
        │ Scrapes every 10s
        │
   ┌────┴──────────────────────────┐
   │ Laravel App Metrics Endpoint   │
   │ http://localhost:8000/metrics  │
   │ (MetricsController)            │
   └────────────────┬───────────────┘
                    │
            ┌───────▼──────────┐
            │   MySQL Database │
            │ (Attendance data)│
            └──────────────────┘
```

## 📋 Service URLs

| Service | URL | Credentials |
|---------|-----|-------------|
| Grafana | http://localhost:3000 | admin/admin |
| Prometheus | http://localhost:9090 | - |
| Laravel App | http://localhost:8000 | - |
| phpMyAdmin | http://localhost:8081 | root/root |
| Metrics Endpoint | http://localhost:8000/metrics | - |

## 🔧 Configuration Files

### Prometheus Configuration
**File**: `prometheus.yml`
- Scrape interval: 10 seconds
- Data retention: 30 days
- Targets: Laravel app metrics endpoint

### Grafana Configuration
**File**: `grafana/grafana.ini`
- Admin user: admin
- Admin password: admin
- Auto-provisioning enabled

### Docker Compose
**File**: `docker-compose.yml`
- Services: MySQL, Prometheus, Grafana, phpMyAdmin
- Networks: Internal `staff-network`
- Volumes: Persistent storage for all services

## 📊 Dashboard Features

### Auto-Refresh Mechanism
```
Every 10 seconds:
1. Dashboard queries Grafana datasource
2. Grafana queries Prometheus
3. Prometheus scrapes Laravel /metrics endpoint
4. Laravel calculates fresh statistics from database
5. Display updates in real-time
```

### Metric Calculation
All metrics are calculated in real-time from the `attendance` table:

```sql
SELECT COUNT(*) FROM attendance 
WHERE attendance_date = TODAY() 
AND status = 'present'
```

## 🚦 Common Tasks

### Check Metrics are Being Scraped
```bash
# View Prometheus targets
curl http://localhost:9090/api/v1/targets

# View raw metrics
curl http://localhost:8000/metrics
```

### Add Attendance Records (for testing)
```bash
# Access Laravel Tinker
docker-compose exec app php artisan tinker

# Create test record
Attendance::create(['staff_id' => 1, 'attendance_date' => now(), 'status' => 'present']);
```

### View Prometheus Data
1. Go to http://localhost:9090
2. Click "Graph" tab
3. Enter metric name: `attendance_present_today`
4. Click "Execute"

### Query via PromQL
```
# Top 5 metrics
topk(5, attendance_by_status)

# Rate of change
rate(attendance_present_today[5m])

# Range query
attendance_present_today offset 1d
```

## 🔍 Monitoring & Alerts (Future)

### Set Up Alerts
```yaml
# Add to prometheus.yml
alerting:
  alertmanagers:
    - static_configs:
      - targets: ['alertmanager:9093']

rule_files:
  - '/etc/prometheus/rules/*.yml'
```

### Example Alert Rule
```yaml
- alert: LowAttendance
  expr: attendance_present_today < 10
  for: 5m
  annotations:
    summary: "Low attendance detected"
```

## 📝 API Integration

### Metrics Endpoint Response
```
# HELP attendance_present_today Total staff present today
# TYPE attendance_present_today gauge
attendance_present_today {job="laravel-app"} 15

# HELP attendance_absent_today Total staff absent today
# TYPE attendance_absent_today gauge
attendance_absent_today {job="laravel-app"} 3
```

### Query Prometheus HTTP API
```bash
# Current value
curl 'http://localhost:9090/api/v1/query?query=attendance_present_today'

# Range query
curl 'http://localhost:9090/api/v1/query_range?query=attendance_present_today&start=1700000000&end=1700100000&step=10s'
```

## 🐛 Troubleshooting

### Metrics Not Appearing
```bash
# 1. Check Laravel app is running
curl http://localhost:8000/login

# 2. Check metrics endpoint
curl http://localhost:8000/metrics

# 3. View Prometheus logs
docker logs prometheus_staff

# 4. Check Prometheus targets
curl http://localhost:9090/api/v1/targets
```

### Grafana Can't Connect to Prometheus
```bash
# Check datasource configuration
# Go to http://localhost:3000/datasources
# Edit Prometheus
# URL should be: http://prometheus:9090
```

### Docker Port Conflicts
```bash
# Find process using port
netstat -ano | findstr :3000

# Stop it or use different port in docker-compose.yml
```

See `TROUBLESHOOTING_GRAFANA.md` for comprehensive troubleshooting guide.

## 📚 Documentation Files

- **GRAFANA_PROMETHEUS_SETUP.md** - Detailed setup guide
- **TROUBLESHOOTING_GRAFANA.md** - Common issues and solutions
- **prometheus.yml** - Prometheus configuration
- **docker-compose.yml** - Docker services configuration

## 🎯 Next Steps

1. ✅ Verify dashboard loads at http://localhost:3000
2. ✅ Add attendance records via Laravel app
3. ✅ Watch metrics update in real-time (10s refresh)
4. ✅ Create custom dashboards for different departments
5. ✅ Set up alerts for low attendance
6. ✅ Export reports for management

## 💡 Tips & Tricks

### Custom Time Range
- Dashboard dropdown (top) allows custom date ranges
- Default: Last 7 days

### Full Screen Mode
- Click panel title → "View" to see full screen

### Download Dashboard JSON
- Click dashboard title → "Settings" → "JSON Model"
- Copy and backup or share with team

### Increase Data Retention
Edit `docker-compose.yml`:
```yaml
prometheus:
  command:
    - '--storage.tsdb.retention.time=90d'  # Change from 30d
```

## 🔐 Security Notes

- Change Grafana admin password: `admin/admin` → your password
- Consider nginx reverse proxy for production
- Enable HTTPS/SSL
- Add authentication to /metrics endpoint (optional)

## 📊 Performance

- Metrics calculation: ~100ms
- Data retention: 30 days (default)
- Storage: ~1GB per month
- Refresh rate: 10 seconds (configurable)

## 🎓 Learning Resources

- [Prometheus Tutorial](https://prometheus.io/docs/prometheus/latest/getting_started/)
- [Grafana Dashboard Guide](https://grafana.com/docs/grafana/latest/dashboards/)
- [PromQL Guide](https://prometheus.io/docs/prometheus/latest/querying/basics/)

## 📞 Support

For issues:
1. Check TROUBLESHOOTING_GRAFANA.md
2. Review logs: `docker-compose logs -f`
3. Test metrics endpoint: `curl http://localhost:8000/metrics`
4. Verify database has data: Check phpMyAdmin

---

**Setup Date**: November 25, 2025
**Components**: Prometheus 2.x, Grafana 10.x, Laravel 12.x, MySQL 8.0
**Refresh Rate**: 10 seconds (configurable)
**Status**: ✅ Ready for Production Monitoring
