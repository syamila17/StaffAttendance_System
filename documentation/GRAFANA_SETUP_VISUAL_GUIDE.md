# VISUAL GUIDE: SETTING UP GRAFANA PIE CHART

## Prerequisites
- ✅ Grafana running on http://localhost:3000
- ✅ MySQL running with staffAttend_data database
- ✅ Attendance records in database for current month

---

## STEP 1: Login to Grafana

```
1. Open: http://localhost:3000
2. You'll see login screen
3. Username: admin
4. Password: admin
5. Click "Log in"
```

---

## STEP 2: Add MySQL Data Source

### Navigate to Data Sources
```
1. Click gear icon ⚙️ on left sidebar
2. Select "Data Sources" from dropdown
   (or go to: http://localhost:3000/datasources)
```

### Create New Data Source
```
1. Click blue "+ Add data source" button
2. Search for "MySQL"
3. Click "MySQL" option from list
```

### Fill Configuration
```
Name:                   MySQL Attendance
Description:            Staff attendance database
Host:                   mysql:3306
Database:               staffAttend_data
User:                   root
Password:               root
TLS/SSL Mode:           skip
MySQL Version:          8.0.x
```

### Test Connection
```
1. Scroll to bottom
2. Click "Save & Test"
3. Should show: ✅ "Database Connection OK"
```

If it fails:
- Check if MySQL container running: `docker ps`
- Verify host is `mysql:3306` (not localhost)
- Check username/password

---

## STEP 3: Create Dashboard

### New Dashboard
```
1. Click "+" icon on left sidebar
2. Click "Dashboard"
3. Click "Add new panel" (or "+ Add panel")
4. You'll see panel editor
```

### Configure Panel Title
```
1. Look for "Panel Title" field at top
2. Enter: "Monthly Attendance Status"
3. Press Enter
```

---

## STEP 4: Set Visualization Type

### Select Pie Chart
```
1. Look at the right panel
2. Find "Visualization" section
3. Look for grid of icons showing different chart types
4. Click the "Pie Chart" icon (circular pie shape)
```

You should see options appear:
- Display Mode: Pie / Donut
- Legend: List / Table
- Tooltip: Hover / Click
- Pie Type: Pie / Donut

### Configure
```
Display Mode:   Pie
Pie Type:       Donut (optional, looks nicer)
Legend:         Table
Tooltip:        Hover
```

---

## STEP 5: Select Data Source

### Set MySQL Datasource
```
1. In Query section (below chart preview)
2. Look for "Data Source" dropdown
3. Select "MySQL Attendance"
```

---

## STEP 6: Enter SQL Query

### Copy the SQL
```
Go to: GRAFANA_SQL_READY_TO_USE.md
Copy the query under: "For Pie Chart - Monthly Breakdown"
```

### Paste in Query Editor
```
1. Find the large text area in Query section
2. Clear any existing text
3. Paste the SQL query
4. You should see results appear in graph
```

### Query to Use
```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        WHEN status = 'leave' THEN 'On Leave'
        ELSE status
    END AS Status,
    COUNT(*) AS Count
FROM attendance
WHERE staff_id = $__myVar  
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
ORDER BY Count DESC
```

---

## STEP 7: Map Fields

### Configure Series and Values
```
In right panel under "Pie Chart":

1. Look for "Series" or "Labels" field
2. Set to: "Status" (the column names)

3. Look for "Values" or "Data" field  
4. Set to: "Count" (the numbers)
```

If you don't see these options:
```
1. Click "Display" or "Options" tab
2. Scroll down to "Field Config"
3. Look for Series and Values fields there
```

---

## STEP 8: Add Variables (Optional but Recommended)

### Create Staff ID Variable
```
1. Click "Dashboard" settings icon (gear) at top
2. Click "Variables" in left submenu
3. Click "+ Add variable"
4. Configure:

Name:           staffId
Type:           Query
Label:          Select Staff
Data Source:    MySQL Attendance
Query:          SELECT DISTINCT staff_id FROM staff
Regex:          (leave blank)
Sort:           Natural sort
Selection:      Multi-value
Include all:    ☑ (checked)

5. Click "Update"
6. Back to dashboard (X to close)
```

### Update Query to Use Variable
```
Replace this line:
WHERE staff_id = $__myVar

With this:
WHERE staff_id = ${staffId}

Then click somewhere else to execute query
```

---

## STEP 9: Customize Colors (Optional)

### Set Color Scheme
```
1. In right panel, find "Color scheme"
2. Click "Value mapping" or "Color mapping"
3. Add mapping for each status:

Add mapping:
- Present → Green
- Absent → Red  
- Late → Yellow
- On Leave → Blue

Or click on the pie chart itself and select colors
```

---

## STEP 10: Save Dashboard

### Save
```
1. Click "Save" button (top right) or Ctrl+S
2. Enter Dashboard Name: "Staff Attendance Dashboard"
3. Choose Folder: General (or create new)
4. Click "Save"
5. You'll see confirmation message
```

### Get Dashboard UID
```
1. Look at URL: http://localhost:3000/d/[UID]/[NAME]
2. Copy the UID part (looks like: abc123def456)
3. Save this for .env configuration
```

---

## STEP 11: Get Panel ID

### Find Panel ID
```
1. In dashboard, click on pie chart
2. Look at top-right of panel
3. Click the dropdown arrow
4. Should see "Panel ID: 1" (or another number)
5. Note this number for .env file
```

Or:
```
1. Right-click pie chart
2. Inspect element
3. Look for panelId in URL or HTML
```

---

## STEP 12: Configure in Laravel

### Update .env File
```
Edit: staff_attendance/.env

Add/Update:
GRAFANA_DASHBOARD_UID=your_dashboard_uid_here
GRAFANA_PIE_CHART_PANEL_ID=1
GRAFANA_URL=http://localhost:3000

Example:
GRAFANA_DASHBOARD_UID=abc123def456
GRAFANA_PIE_CHART_PANEL_ID=1
GRAFANA_URL=http://localhost:3000
```

### Clear Cache
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance

php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## STEP 13: Test in Laravel Dashboard

### View in Browser
```
1. Open: http://localhost:8000/login
2. Login with staff credentials
3. Go to Dashboard
4. You should see pie chart on left side
5. Shows current month attendance breakdown
```

### What You Should See
```
Pie Chart with:
- Green slice: Present days
- Red slice: Absent days
- Yellow slice: Late days
- Blue slice: On Leave days
- Other colors: EL, Half Day

Legend below showing:
✓ Green = Present
✗ Red = Absent
⏱ Yellow = Late
📅 Blue = On Leave
```

---

## TROUBLESHOOTING

### Pie chart shows "No data"
```
1. Check if MySQL datasource is connected
2. Go back to dashboard
3. Click pie chart → Edit
4. Check Query tab - should see data results
5. If no results, check:
   - staff_id variable is set
   - Attendance records exist (use phpMyAdmin)
   - Query syntax is correct
```

### "Database Connection Failed"
```
1. Go to Data Sources settings
2. Click MySQL Attendance
3. Scroll to Test button
4. It will show error message
5. Common issues:
   - Host should be "mysql:3306" not "localhost"
   - MySQL not running (docker ps)
   - Wrong credentials
```

### Variable dropdown shows nothing
```
1. Dashboard settings → Variables
2. Click on staffId variable
3. Click "Test" button
4. Should return list of staff_ids
5. If error, check SQL query and datasource
```

### iframe not showing in Laravel
```
1. Check .env has correct GRAFANA_DASHBOARD_UID
2. Clear cache: php artisan config:clear
3. Check browser console (F12) for errors
4. Verify URL format in controller
```

---

## Verification Checklist

```
☑ Grafana running on http://localhost:3000
☑ Can login (admin/admin)
☑ MySQL datasource shows "Connection OK"
☑ Dashboard created with title
☑ Pie Chart panel added
☑ SQL query shows data in results
☑ Status and Count fields mapped correctly
☑ Dashboard saved with UID
☑ .env updated with correct UID and Panel ID
☑ Laravel cache cleared
☑ Pie chart displays in dashboard
☑ Chart shows current month data
☑ Colors are correct (green/red/yellow/blue)
```

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "No data" | Check attendance records exist in DB |
| Connection failed | Verify host is mysql:3306 |
| Pie chart blank | Test query in phpMyAdmin first |
| Variable empty | Check SQL in variable datasource |
| iframe not showing | Clear Laravel cache, check .env |
| Wrong data shown | Verify staff_id variable is correct |
| No colors | Set pie chart visualization type |

---

## Next Steps

Once pie chart is working:

1. Add more panels to dashboard (table, stats, trends)
2. Create alerts (for high absence rate)
3. Add drill-down functionality
4. Share dashboard with team
5. Create role-based dashboards

---

## Quick Reference

```
Grafana URL:        http://localhost:3000
phpMyAdmin:         http://localhost:8081
Laravel Dashboard:  http://localhost:8000/login

Credentials:
- Grafana: admin/admin
- MySQL: root/root
- phpMyAdmin: root/root

Key Files:
- .env: staffAttend_data/.env
- Dashboard: Staff Attendance Dashboard
- Data Source: MySQL Attendance
- Pie Chart Panel: Monthly Attendance Status
```

