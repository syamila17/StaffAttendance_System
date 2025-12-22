# Grafana Leave Balance Integration Guide

## Overview
Your Laravel application now exposes leave balance metrics to Grafana via Prometheus. These metrics can be visualized in Grafana dashboards.

## Available Metrics

### Leave Balance Metrics
- `leave_total_balance` - Total annual leave balance for all staff (days)
- `leave_used` - Total annual leave used (days)
- `leave_remaining` - Total annual leave remaining (days)
- `leave_requests_pending` - Number of pending leave requests
- `leave_requests_approved` - Number of approved leave requests
- `leave_requests_rejected` - Number of rejected leave requests

### Attendance Metrics (Existing)
- `attendance_present_today` - Staff present today
- `attendance_absent_today` - Staff absent today
- `attendance_late_today` - Staff late today
- `attendance_el_today` - Staff on emergency leave today
- `attendance_leave_today` - Staff on leave today
- `attendance_halfday_today` - Staff on half day today

## Setup Steps

### 1. Verify Prometheus is Scraping
Prometheus should already be configured to scrape metrics from `http://localhost:8000/metrics`

Check prometheus.yml configuration:
```yaml
scrape_configs:
  - job_name: 'laravel-app'
    static_configs:
      - targets: ['localhost:8000']
    metrics_path: '/metrics'
```

### 2. Add Prometheus Data Source in Grafana
1. Go to Grafana: `http://localhost:3000`
2. Login: `admin` / `admin`
3. Click **Settings** (gear icon) → **Data Sources**
4. Click **Add data source**
5. Select **Prometheus**
6. Set URL: `http://prometheus:9090`
7. Click **Save & Test**

### 3. Create Leave Balance Dashboard

#### Method A: Import Pre-built Dashboard (Recommended)

Use this JSON configuration to create a dashboard:

1. Go to **Dashboards** → **New Dashboard** → **Import**
2. Copy the JSON below and paste it
3. Select the Prometheus data source
4. Click **Import**

#### Method B: Manual Dashboard Creation

**Create Gauge Panels:**

1. **Total Leave Balance**
   - Query: `leave_total_balance`
   - Title: "Total Annual Leave Balance"
   - Unit: "short"
   - Thresholds: 0-10 (red), 10-20 (yellow), 20+ (green)

2. **Total Leave Used**
   - Query: `leave_used`
   - Title: "Total Leave Used"
   - Unit: "short"
   - Color: Orange

3. **Total Leave Remaining**
   - Query: `leave_remaining`
   - Title: "Total Leave Remaining"
   - Unit: "short"
   - Color: Green

4. **Pending Requests**
   - Query: `leave_requests_pending`
   - Title: "Pending Leave Requests"
   - Unit: "short"
   - Threshold Alert: > 5

5. **Approved Requests**
   - Query: `leave_requests_approved`
   - Title: "Approved Leave Requests"
   - Unit: "short"

6. **Rejected Requests**
   - Query: `leave_requests_rejected`
   - Title: "Rejected Leave Requests"
   - Unit: "short"

### 4. Visualize as Pie Charts

Create pie charts showing:
- Leave balance distribution (Used vs Remaining)
- Leave request status distribution

**Used vs Remaining:**
```
Queries:
- Query A: `leave_used` as "Used"
- Query B: `leave_remaining` as "Remaining"
Visualization: Pie Chart
```

**Request Status:**
```
Queries:
- Query A: `leave_requests_pending` as "Pending"
- Query B: `leave_requests_approved` as "Approved"
- Query C: `leave_requests_rejected` as "Rejected"
Visualization: Pie Chart
```

### 5. Create Stat Panels

**Stats Overview:**
```
Panel 1: Total Annual Leave
Query: leave_total_balance
Unit: short
Color: Blue

Panel 2: Leave Used This Year
Query: leave_used
Unit: short
Color: Orange

Panel 3: Remaining Leave
Query: leave_remaining
Unit: short
Color: Green

Panel 4: Pending Requests
Query: leave_requests_pending
Unit: short
Color: Yellow
```

## Testing

### 1. Verify Metrics Endpoint
```bash
curl http://localhost:8000/metrics | grep leave_
```

Expected output:
```
# HELP leave_total_balance Total annual leave balance for all staff
# TYPE leave_total_balance gauge
leave_total_balance {job="laravel-app"} 100

# HELP leave_used Total annual leave used
# TYPE leave_used gauge
leave_used {job="laravel-app"} 5

# HELP leave_remaining Total annual leave remaining
# TYPE leave_remaining gauge
leave_remaining {job="laravel-app"} 95

leave_requests_pending {job="laravel-app"} 2
leave_requests_approved {job="laravel-app"} 15
leave_requests_rejected {job="laravel-app"} 3
```

### 2. Check Prometheus Targets
Go to `http://localhost:9090/targets` and verify the Laravel app is "UP"

### 3. Query in Prometheus
Go to `http://localhost:9090` and test queries:
- `leave_total_balance`
- `leave_used`
- `leave_remaining`

## Dashboard JSON Example

You can import this JSON as a complete dashboard:

```json
{
  "dashboard": {
    "title": "Staff Leave Balance",
    "tags": ["leave", "attendance"],
    "timezone": "browser",
    "panels": [
      {
        "title": "Total Annual Leave",
        "targets": [
          {
            "expr": "leave_total_balance",
            "legendFormat": "Total Balance"
          }
        ],
        "type": "stat",
        "fieldConfig": {
          "defaults": {
            "unit": "short",
            "custom": {}
          }
        }
      },
      {
        "title": "Leave Used",
        "targets": [
          {
            "expr": "leave_used",
            "legendFormat": "Used"
          }
        ],
        "type": "stat"
      },
      {
        "title": "Leave Remaining",
        "targets": [
          {
            "expr": "leave_remaining",
            "legendFormat": "Remaining"
          }
        ],
        "type": "stat"
      },
      {
        "title": "Leave Requests Status",
        "targets": [
          {
            "expr": "leave_requests_pending",
            "legendFormat": "Pending"
          },
          {
            "expr": "leave_requests_approved",
            "legendFormat": "Approved"
          },
          {
            "expr": "leave_requests_rejected",
            "legendFormat": "Rejected"
          }
        ],
        "type": "piechart"
      }
    ]
  }
}
```

## Troubleshooting

### Metrics not appearing in Grafana
1. Check if Laravel app is running: `http://localhost:8000/metrics`
2. Check Prometheus is scraping: `http://localhost:9090/targets`
3. Wait 15-30 seconds for metrics to appear (default scrape interval)
4. Check Prometheus has data: `http://localhost:9090/graph`

### Data shows 0
- Verify staff records exist in the database
- Check leave requests are created with correct dates
- Run a test leave request to populate data

### Connection Issues
- Verify Docker network: all containers are on `staffattendance_system_staff-network`
- Check Prometheus target shows "UP" status
- Verify no firewall blocking port 8000 or 9090

## Notes

- Leave calculations are based on year-to-date (Jan 1 - Dec 31)
- Annual leave default is 20 days
- Metrics update on every request to `/metrics` endpoint
- Prometheus scrapes metrics every 15 seconds (configurable)
- Historical data retained per Prometheus retention policy (15 days default)

