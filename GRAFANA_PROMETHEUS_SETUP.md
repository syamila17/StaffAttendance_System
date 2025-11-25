# Grafana & Prometheus Setup Guide

## Overview
This setup provides real-time attendance statistics visualization using Grafana and Prometheus with auto-refreshing metrics every 10 seconds.

## Services
- **Prometheus** (Port 9090): Time-series metrics database
- **Grafana** (Port 3000): Visualization dashboard
- **Laravel App** (Port 8000): Metrics endpoint at `/metrics`
- **MySQL** (Port 3307): Database
- **phpMyAdmin** (Port 8081): Database management

## Quick Start

### 1. Start All Services
```bash
cd c:\Users\syami\Desktop\StaffAttendance_System
docker-compose up -d
```

### 2. Access Grafana
- **URL**: http://localhost:3000
- **Username**: admin
- **Password**: admin

### 3. Access Prometheus
- **URL**: http://localhost:9090
- **Metrics Endpoint**: http://localhost:8000/metrics

## Grafana Dashboard

The dashboard will automatically load with the following panels:

### Real-time Metrics (Auto-refresh every 10 seconds)
1. **Total Present Today** - Green stat card
2. **Total Absent Today** - Red stat card
3. **Total Late Today** - Yellow stat card
4. **Total On Leave Today** - Blue stat card
5. **Attendance Status Breakdown** - Pie chart showing all statuses
6. **Daily Attendance Trend** - 7-day line chart showing trends

### Metrics Available
- `attendance_present_today` - Staff present count
- `attendance_absent_today` - Staff absent count
- `attendance_late_today` - Staff late count
- `attendance_el_today` - Staff on emergency leave
- `attendance_leave_today` - Staff on leave
- `attendance_halfday_today` - Staff on half day
- `attendance_total_staff` - Total staff with records today
- `attendance_by_status` - Breakdown by status
- `attendance_daily_*` - Historical data for trends

## Docker Compose Structure

```yaml
Services:
├── mysql (Database)
├── phpmyadmin (DB UI)
├── prometheus (Metrics Database)
├── grafana (Visualization)

Networks:
└── staff-network (internal communication)

Volumes:
├── mysql_data (persistent database)
├── prometheus_data (persistent metrics)
└── grafana_data (persistent dashboards)
```

## Metrics Scraping Configuration

**Prometheus scrapes:**
- Laravel app `/metrics` endpoint every 10 seconds
- Prometheus itself every 10 seconds

**Prometheus settings:**
- Retention: 30 days
- Scrape interval: 10 seconds
- Evaluation interval: 10 seconds

## Accessing Metrics

### Raw Metrics (Prometheus format)
```
GET http://localhost:8000/metrics
```

Returns metrics in Prometheus text format:
```
# HELP attendance_present_today Total staff present today
# TYPE attendance_present_today gauge
attendance_present_today {job="laravel-app"} 15
```

### Via Prometheus UI
1. Go to http://localhost:9090
2. Click "Graph" tab
3. Enter metric name (e.g., `attendance_present_today`)
4. Click "Execute"

### Via Grafana Dashboard
1. Go to http://localhost:3000
2. Select "Staff Attendance Statistics" dashboard
3. Dashboard auto-refreshes every 10 seconds

## Troubleshooting

### Prometheus not scraping metrics
```bash
# Check Prometheus status
curl http://localhost:9090/api/v1/targets

# View Prometheus logs
docker logs prometheus_staff
```

### Metrics endpoint not working
```bash
# Test metrics endpoint
curl http://localhost:8000/metrics

# Check Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log
```

### Grafana datasource issues
1. Go to http://localhost:3000/datasources
2. Edit "Prometheus" datasource
3. Set URL to: `http://prometheus:9090`
4. Click "Save & Test"

## Custom Metrics

To add more metrics, edit `app/Http/Controllers/MetricsController.php` and add new gauge/counter metrics.

Example:
```php
$metrics .= "# HELP custom_metric Custom metric description\n";
$metrics .= "# TYPE custom_metric gauge\n";
$metrics .= "custom_metric {job=\"laravel-app\"} 42\n\n";
```

## Grafana Customization

### Change Dashboard Refresh Rate
1. Click dashboard title → "Settings"
2. Find "Refresh" setting
3. Change from 10s to desired interval

### Add New Panels
1. Click "+ Add panel"
2. Select query type (Prometheus)
3. Enter metric expression
4. Configure visualization

### Export/Import Dashboards
1. Click dashboard title → "Settings" → "JSON Model"
2. Copy JSON to backup or share

## Performance Notes

- Metrics calculation: < 100ms
- Data retention: 30 days (configurable in docker-compose.yml)
- Storage: ~1GB per month (typical usage)

## Next Steps

1. Monitor real-time attendance patterns
2. Set up alerts for low attendance
3. Create custom dashboards for different departments
4. Export reports for management review
