# Grafana Attendance Dashboard Integration Guide

## Overview

This guide shows how to set up a Grafana pie chart that displays attendance statistics by status (Present, Absent, Late) for the selected month in your Laravel dashboard.

---

## Features Implemented

✅ **Embedded Grafana Iframe** - Pie chart showing attendance breakdown  
✅ **Month Selector Dropdown** - Choose any month from the last 12 months  
✅ **Manual Refresh Button** - Click to refresh the chart anytime  
✅ **Auto-Refresh** - Chart automatically refreshes every 30 seconds  
✅ **Responsive Design** - Works on all screen sizes  
✅ **Dark Theme** - Matches your application's color scheme  
✅ **Last Updated Timestamp** - Shows when chart was last refreshed  

---

## Prerequisites

1. **Grafana Running** on `http://localhost:3000`
2. **MySQL Data Source** configured in Grafana
3. **Attendance Dashboard** created in Grafana with a pie chart panel

---

## Step 1: Create Grafana Data Source

### Via Grafana UI:

1. Go to: `http://localhost:3000` → Settings (gear icon) → Data Sources
2. Click "Add data source"
3. Select "MySQL"
4. Configure:
   - **Name**: `MySQL Attendance`
   - **Host**: `localhost:3307` (or your MySQL port)
   - **Database**: `staffAttend_data`
   - **User**: `root`
   - **Password**: `your_password`
5. Click "Save & Test"

---

## Step 2: Create Grafana Dashboard

### Dashboard Setup:

1. Create new dashboard: Dashboards → New → Dashboard
2. Name it: `Attendance Dashboard` (slug: `attendance-dashboard`)
3. Add a new panel

### Add Pie Chart Panel:

1. Click "Add panel" → "Pie chart"
2. Configure the SQL query:

```sql
SELECT 
  CASE 
    WHEN status = 'present' THEN 'Present'
    WHEN status = 'absent' THEN 'Absent'
    WHEN status = 'late' THEN 'Late'
    WHEN status = 'el' THEN 'Emergency Leave'
    WHEN status = 'on leave' THEN 'On Leave'
    WHEN status = 'half day' THEN 'Half Day'
    ELSE 'Other'
  END as Status,
  COUNT(*) as Count
FROM attendance
WHERE staff_id = $staffId
  AND YEAR(attendance_date) = $year
  AND MONTH(attendance_date) = $month
GROUP BY status
ORDER BY Count DESC
```

### Configure Panel Settings:

**Legend:**
- Display legend: **On**
- Legend values: Show names and values

**Pie Chart Options:**
- Display labels: **On**
- Tooltip: **Multi-series sort descending**
- Tooltip sort order: **Descending**

**Color Scheme:**
- Scheme: **Classic palette**
- Manual colors:
  - Present = Green (#31A86D)
  - Absent = Red (#E02528)
  - Late = Yellow (#FBB830)

### Note Panel ID:
When you create the panel, note the **Panel ID** (usually 1 for first panel)

---

## Step 3: Dashboard Configuration File

If using provisioning, create `grafana/provisioning/dashboards/attendance-dashboard.json`:

```json
{
  "dashboard": {
    "title": "Attendance Dashboard",
    "slug": "attendance-dashboard",
    "panels": [
      {
        "id": 1,
        "title": "Attendance by Status",
        "type": "piechart",
        "targets": [
          {
            "query": "SELECT CASE WHEN status = 'present' THEN 'Present' WHEN status = 'absent' THEN 'Absent' WHEN status = 'late' THEN 'Late' ELSE 'Other' END as Status, COUNT(*) as Count FROM attendance WHERE staff_id = ${STAFF_ID} AND YEAR(attendance_date) = ${YEAR} AND MONTH(attendance_date) = ${MONTH} GROUP BY status"
          }
        ]
      }
    ]
  }
}
```

---

## Step 4: Update Blade Template

The dashboard has been updated with:

### Month Selector Dropdown
```blade
<select id="monthSelector" onchange="updateGrafanaChart()">
  @for($i = 0; $i < 12; $i++)
    @php $date = \Carbon\Carbon::now()->subMonths($i); @endphp
    <option value="{{ $date->format('Y-m') }}">
      {{ $date->format('F Y') }}
    </option>
  @endfor
</select>
```

### Grafana Iframe
```blade
<iframe id="grafanaChart" 
  src="http://localhost:3000/d-solo/attendance/attendance-dashboard?orgId=1&panelId=1&from=now-30d&to=now&theme=dark&kiosk" 
  width="100%" 
  height="500px" 
  frameborder="0">
</iframe>
```

### JavaScript Functions

**updateGrafanaChart()** - Changes the date range based on selected month

**refreshGrafanaChart()** - Manually refresh the chart

**Auto-refresh every 30 seconds** - Automatic updates

---

## Step 5: JavaScript Implementation

### Key Functions:

#### 1. Update Chart on Month Selection
```javascript
function updateGrafanaChart() {
  const selectedMonth = document.getElementById('monthSelector').value;
  const [year, month] = selectedMonth.split('-');
  
  // Calculate date range
  const firstDay = new Date(year, month - 1, 1);
  const lastDay = new Date(year, month, 0);
  
  const fromTime = Math.floor(firstDay.getTime());
  const toTime = Math.floor(lastDay.getTime());
  
  // Update iframe URL
  const grafanaChart = document.getElementById('grafanaChart');
  grafanaChart.src = `http://localhost:3000/d-solo/attendance/attendance-dashboard?orgId=1&panelId=1&from=${fromTime}&to=${toTime}&theme=dark&kiosk&_t=${Date.now()}`;
}
```

#### 2. Manual Refresh
```javascript
function refreshGrafanaChart() {
  const grafanaChart = document.getElementById('grafanaChart');
  const currentSrc = grafanaChart.src;
  grafanaChart.src = currentSrc + (currentSrc.includes('?') ? '&' : '?') + `_t=${Date.now()}`;
}
```

#### 3. Auto-Refresh Every 30 Seconds
```javascript
setInterval(() => {
  refreshGrafanaChart();
}, 30000);
```

---

## URL Parameters Explained

| Parameter | Value | Purpose |
|-----------|-------|---------|
| `orgId` | 1 | Grafana organization ID |
| `panelId` | 1 | Panel ID in the dashboard |
| `from` | Timestamp | Start date of date range |
| `to` | Timestamp | End date of date range |
| `theme` | dark | Use dark theme |
| `kiosk` | (flag) | Fullscreen mode (no UI chrome) |
| `_t` | Timestamp | Cache buster for refresh |

### Example URLs:

**Current Month:**
```
http://localhost:3000/d-solo/attendance/attendance-dashboard?orgId=1&panelId=1&from=now-30d&to=now&theme=dark
```

**Specific Month (Dec 2025):**
```
http://localhost:3000/d-solo/attendance/attendance-dashboard?orgId=1&panelId=1&from=1733011200000&to=1735689600000&theme=dark
```

---

## Customization Options

### Change Chart Colors

In Grafana dashboard settings, customize the color palette:
- **Green** for Present status
- **Red** for Absent status
- **Yellow** for Late status

### Change Auto-Refresh Interval

In `staff_dashboard.blade.php`, modify:
```javascript
// Currently 30 seconds (30000 ms)
setInterval(() => {
  refreshGrafanaChart();
}, 30000); // Change this value
```

Other common intervals:
- 10 seconds: `10000`
- 60 seconds: `60000`
- 5 minutes: `300000`

### Change Chart Height

In the Blade template:
```blade
<!-- Currently 500px -->
<div style="height: 500px;">
  <iframe ... ></iframe>
</div>
```

---

## Troubleshooting

### Chart Not Loading

**Problem**: Iframe shows blank  
**Solution**:
1. Verify Grafana is running: `http://localhost:3000`
2. Check dashboard exists: Settings → Dashboard List
3. Check panel ID is correct (usually `panelId=1`)
4. Check data source has data

### Chart Shows No Data

**Problem**: Pie chart is empty  
**Solution**:
1. Verify MySQL query in Grafana:
   - Go to panel → Edit → Query
   - Run query manually in MySQL
   - Check if attendance table has data for selected month
2. Check staff_id variables are correct
3. Verify date range includes data

### Month Selector Not Working

**Problem**: Selecting month doesn't update chart  
**Solution**:
1. Check browser console (F12) for JavaScript errors
2. Verify `updateGrafanaChart()` function is defined
3. Check month selector ID: `id="monthSelector"`

### Auto-Refresh Not Working

**Problem**: Chart doesn't update automatically  
**Solution**:
1. Check browser console for JavaScript errors
2. Verify `setInterval` is running
3. Check if Grafana cache is preventing updates
4. Try manual refresh button first

### CORS Issues

**Problem**: Mixed content or CORS error  
**Solution**:
1. Ensure Grafana URL uses same protocol as app (http/https)
2. If CORS still issues, configure Grafana:
   - Edit `grafana.ini`
   - Add: `allow_embedding = true`
   - Restart Grafana

---

## Security Considerations

### For Production:

1. **Use HTTPS**: Change URLs from `http://` to `https://`
2. **Authenticate**: Add Grafana authentication to iframe
3. **Limit Access**: Configure Grafana user permissions
4. **Hide Credentials**: Use environment variables for Grafana URL

### Example with Environment Variables:

```blade
<iframe src="{{ env('GRAFANA_URL') }}/d-solo/attendance/attendance-dashboard?..." ></iframe>
```

In `.env`:
```
GRAFANA_URL=https://grafana.yourdomain.com
```

---

## Performance Tips

1. **Limit Data Range**: Don't query too many months at once
2. **Cache Dashboard**: Grafana caches queries by default
3. **Limit Panel Updates**: Auto-refresh every 30+ seconds (don't overload)
4. **Use Indexes**: Add indexes on `attendance.staff_id` and `attendance.attendance_date`

---

## MySQL Query Optimization

Add indexes for better performance:

```sql
ALTER TABLE attendance ADD INDEX idx_staff_id_date (staff_id, attendance_date);
ALTER TABLE attendance ADD INDEX idx_status (status);
```

---

## Next Steps

1. ✅ Create Grafana data source (MySQL)
2. ✅ Create Attendance dashboard with pie chart
3. ✅ Note the panel ID
4. ✅ Update iframe URL in Blade template with correct URLs
5. ✅ Test month selector
6. ✅ Test manual refresh button
7. ✅ Verify auto-refresh works

---

## Files Modified

- `resources/views/staff_dashboard.blade.php` - Added pie chart section with month selector

---

## References

- [Grafana Embedding Docs](https://grafana.com/docs/grafana/latest/dashboards/build-dashboards/manage-dashboards/#embed-panels)
- [Grafana URL Parameters](https://grafana.com/docs/grafana/latest/dashboards/manage-dashboards/#dashboard-urls)
- [MySQL Data Source](https://grafana.com/docs/grafana/latest/datasources/mysql/)

---

**Status**: Ready to use  
**Last Updated**: December 5, 2025
