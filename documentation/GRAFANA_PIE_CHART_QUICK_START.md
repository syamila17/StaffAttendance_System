# Quick Setup: Grafana Attendance Pie Chart

## What Was Added

Your staff dashboard now includes:
- 📊 **Embedded Grafana Pie Chart** showing attendance breakdown by status
- 📅 **Month Selector** to view any month from the last 12 months  
- 🔄 **Auto-Refresh** every 30 seconds
- 🔘 **Manual Refresh Button** for instant updates
- 📱 **Responsive Design** that works on all devices

---

## Quick Start (5 minutes)

### Step 1: Verify Grafana is Running
```bash
# Check if Grafana container is running
docker-compose ps

# If not running, start it:
docker-compose up -d grafana
```

Visit `http://localhost:3000` → Should see Grafana login

### Step 2: Create MySQL Data Source

1. Login to Grafana (admin/admin by default)
2. Go to: ⚙️ Settings → Data Sources → Add data source
3. Select **MySQL**
4. Configure:
   - Name: `MySQL Attendance`
   - Host: `mysql:3307`
   - Database: `staffAttend_data`
   - User: `root`
   - Password: `your_password`
5. Click "Save & Test"

### Step 3: Create Dashboard

1. Click "Create" → "Dashboard"
2. Name it: `Attendance Dashboard`
3. Add Panel → Pie Chart
4. Paste this query in the SQL section:

```sql
SELECT 
  CASE 
    WHEN status = 'present' THEN 'Present'
    WHEN status = 'absent' THEN 'Absent'
    WHEN status = 'late' THEN 'Late'
    ELSE 'Other'
  END as Status,
  COUNT(*) as Count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
  AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
```

5. Configure:
   - Visualization: **Pie chart**
   - Title: "Attendance by Status"
6. Save dashboard (Slug: `attendance-dashboard`)

### Step 4: Get Panel ID

1. Edit the pie chart panel
2. Look at the URL: `?panelId=1` (this is your panel ID)
3. Note it down

### Step 5: Update Dashboard URL

In `staff_dashboard.blade.php`, find the iframe URL and update:
```javascript
// Update these if different:
// - orgId: Your Grafana org ID (usually 1)
// - panelId: Panel ID from Step 4
// - Dashboard slug: "attendance-dashboard"

http://localhost:3000/d-solo/attendance/attendance-dashboard?orgId=1&panelId=1&theme=dark&kiosk
```

### Step 6: Test It!

1. Go to Staff Dashboard
2. You should see the pie chart
3. Test month selector (should update chart)
4. Test refresh button
5. Wait 30 seconds (should auto-refresh)

---

## Features Explained

### 📅 Month Selector
- Dropdown showing last 12 months
- Select any month to view attendance for that month
- Chart updates immediately

### 🔄 Auto-Refresh
- Updates every 30 seconds automatically
- Shows "Last updated" timestamp
- Continues in the background

### 🔘 Refresh Button
- Click to manually refresh chart
- Useful when data changes
- Updates timestamp

### 📊 Pie Chart
- **Green**: Present (majority usually)
- **Red**: Absent
- **Yellow**: Late
- **Other colors**: Other statuses

---

## Customization

### Change Auto-Refresh Interval

Edit `staff_dashboard.blade.php` JavaScript:
```javascript
// Change from 30000 (30 seconds) to:
setInterval(() => {
  refreshGrafanaChart();
}, 60000); // 60 seconds

// Or:
}, 10000); // 10 seconds
```

### Change Chart Height

Edit the HTML:
```blade
<!-- Change from 500px to your preferred height -->
<div style="height: 600px;">
  <iframe id="grafanaChart" ... ></iframe>
</div>
```

### Change Chart Colors

In Grafana:
1. Edit panel
2. Go to "Options" tab
3. Expand "Legend"
4. Customize colors for each status

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Chart shows blank/empty | Verify Grafana is running; check MySQL has attendance data |
| Month selector doesn't work | Check browser console (F12) for errors |
| Chart doesn't auto-refresh | Check setInterval in JavaScript; clear browser cache |
| CORS error | Ensure Grafana URL matches (http/https) |
| No data in chart | Query MySQL directly: `SELECT * FROM attendance LIMIT 10;` |

---

## Docker Command Reference

```bash
# View Grafana logs
docker-compose logs grafana

# Stop Grafana
docker-compose stop grafana

# Start Grafana
docker-compose start grafana

# Restart Grafana
docker-compose restart grafana

# View all containers
docker-compose ps
```

---

## MySQL Query Tips

### View Raw Attendance Data
```sql
SELECT 
  staff_id,
  attendance_date,
  status,
  check_in_time,
  check_out_time
FROM attendance
WHERE YEAR(attendance_date) = 2025
  AND MONTH(attendance_date) = 12
ORDER BY attendance_date DESC
LIMIT 20;
```

### Count by Status
```sql
SELECT 
  status,
  COUNT(*) as total
FROM attendance
WHERE YEAR(attendance_date) = 2025
  AND MONTH(attendance_date) = 12
GROUP BY status;
```

### Check for Data Issues
```sql
SELECT 
  COUNT(*) as total_records,
  COUNT(DISTINCT staff_id) as unique_staff,
  MIN(attendance_date) as first_date,
  MAX(attendance_date) as last_date
FROM attendance;
```

---

## Files Added/Modified

### Modified:
- `resources/views/staff_dashboard.blade.php`
  - Added Grafana pie chart section
  - Added month selector dropdown
  - Added JavaScript for chart updates

### Created:
- `GRAFANA_DASHBOARD_SETUP.md` - Detailed setup guide
- This file - Quick reference

---

## Features Checklist

- ✅ Embedded Grafana iframe
- ✅ Month selector dropdown
- ✅ Auto-refresh every 30 seconds
- ✅ Manual refresh button
- ✅ Last updated timestamp
- ✅ Responsive design (works on mobile)
- ✅ Dark theme matches app
- ✅ Color-coded status legend

---

## Next Steps

1. Run Docker containers (if not already running)
2. Create MySQL data source in Grafana
3. Create Attendance dashboard with pie chart
4. Update iframe URL with your panel ID
5. Test the dashboard!

---

**Need Help?** See `GRAFANA_DASHBOARD_SETUP.md` for detailed instructions
