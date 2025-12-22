# GRAFANA PIE CHART INTEGRATION - QUICK START

## Summary
Your Laravel dashboard is now configured to display a Grafana pie chart showing monthly attendance statistics. The pie chart automatically updates every 30 seconds.

---

## WHAT'S BEEN SET UP

### 1. Database Connection
✅ MySQL datasource will be configured in Grafana  
✅ SQL queries are provided for pie chart data  
✅ Variables support dynamic staff filtering  

### 2. Laravel Backend
✅ StaffController updated to generate Grafana iframe URLs  
✅ Dashboard passes staff_id to Grafana automatically  
✅ .env configured with Grafana settings  

### 3. Dashboard View
✅ Staff_dashboard.blade.php updated with Grafana iframe  
✅ Statistics boxes show monthly breakdown  
✅ Legend shows all attendance statuses  

---

## STEP-BY-STEP SETUP

### STEP 1: Access Grafana (5 minutes)

```
1. Open browser: http://localhost:3000
2. Login: admin / admin
3. Change password (recommended)
```

### STEP 2: Add MySQL Data Source (3 minutes)

```
1. Click gear icon (⚙️) → Data Sources
2. Click "+ Add data source"
3. Select MySQL
4. Fill in:
   - Name: MySQL Attendance
   - Host: mysql:3306
   - Database: staffAttend_data
   - User: root
   - Password: root
5. Click "Save & Test"
   - You should see: ✅ "Database Connection OK"
```

### STEP 3: Create Dashboard & Pie Chart (10 minutes)

```
1. Click + icon → Dashboard → Add new panel
2. Set title: "Monthly Attendance Status"
3. Select MySQL Attendance datasource
4. Choose Pie Chart visualization
```

### STEP 4: Add SQL Query (2 minutes)

Copy this SQL into the Query editor:

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

### STEP 5: Configure Pie Chart (2 minutes)

```
1. Set Status as Series (row labels)
2. Set Count as Values (numbers)
3. Choose display mode: Pie
4. Optional: Customize colors
   - Present: Green (#22c55e)
   - Absent: Red (#ef4444)
   - Late: Yellow (#eab308)
   - On Leave: Blue (#3b82f6)
```

### STEP 6: Add Variables (3 minutes)

```
1. Dashboard Settings → Variables → Add variable
2. Create "staffId" variable:
   - Type: Query
   - Query: SELECT DISTINCT staff_id FROM staff
   - Include all option: On
3. Update SQL query:
   WHERE staff_id = ${staffId}
```

### STEP 7: Save Dashboard (1 minute)

```
1. Click Save (top right)
2. Dashboard name: "Staff Attendance Dashboard"
3. Copy the dashboard UID from URL (looks like: abc123def456)
4. Note: You need this for the Laravel integration
```

### STEP 8: Get Panel ID (1 minute)

```
1. Click on your pie chart panel
2. Copy the Panel ID from the URL or title bar
3. Default is usually: 1
```

### STEP 9: Update .env File

Edit: `staff_attendance/.env`

```
# Add/Update these values:
GRAFANA_DASHBOARD_UID=YOUR_DASHBOARD_UID_HERE
GRAFANA_PIE_CHART_PANEL_ID=1
```

Example:
```
GRAFANA_DASHBOARD_UID=abc123def456
GRAFANA_PIE_CHART_PANEL_ID=1
```

### STEP 10: Clear Laravel Cache

```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### STEP 11: Test in Browser

```
1. Open: http://localhost:8000/login
2. Login with staff credentials
3. Go to Dashboard
4. The pie chart should display with Grafana data
```

---

## WHAT THE PIE CHART SHOWS

The pie chart displays the current month's attendance breakdown:

- **Present**: Days marked as present (green)
- **Absent**: Days marked as absent (red)
- **Late**: Days marked as late (yellow)
- **On Leave**: Days on approved leave (blue)
- **EL**: Emergency leaves (orange)
- **Half Day**: Half day records (purple)

**Auto-refresh**: Every 30 seconds (configurable)

---

## TROUBLESHOOTING

### "Pie chart not showing"
✓ Check Grafana is running: `docker ps`
✓ Verify MySQL datasource is connected
✓ Check staff has attendance records
✓ Clear browser cache (Ctrl+Shift+Delete)

### "Database Connection Failed in Grafana"
✓ Verify MySQL container: `docker ps | findstr mysql`
✓ Check host is `mysql:3306` (not localhost)
✓ Check credentials: root / root

### "No data in pie chart"
✓ Run SQL query manually in phpMyAdmin to test
✓ Check staff_id exists in attendance table
✓ Verify current month has attendance records

### "Variable not working"
✓ Refresh page after adding variable
✓ Ensure variable name matches in query: `${staffId}`
✓ Check datasource connection

---

## FILES TO REFERENCE

1. **GRAFANA_SETUP_COMPLETE_GUIDE.md** - Full detailed setup guide
2. **GRAFANA_SQL_QUERIES.sql** - All available SQL queries
3. **staff_attendance/.env** - Configuration file with Grafana settings
4. **app/Http/Controllers/StaffController.php** - Controller with Grafana URL generation
5. **resources/views/staff_dashboard.blade.php** - View with Grafana iframe

---

## NEXT STEPS (Optional)

Once pie chart is working:

1. Add more panels:
   - Table showing detailed records
   - Stats boxes for totals
   - Trend chart showing last 12 months

2. Create alerts for:
   - Excessive absences
   - Consistent lateness

3. Export dashboard as JSON for backup

4. Create role-based dashboards:
   - Staff: View own attendance
   - Manager: View team attendance
   - Admin: View all staff

---

## SUPPORT

If you encounter issues:

1. Check Grafana logs: `docker logs grafana_staff`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test database connection: `php artisan tinker` → `DB::connection()->getPdo()`
4. Verify Docker network: `docker network ls`

---

## TIME ESTIMATE

Total setup time: **30-45 minutes**

- Grafana MySQL connection: 5 min
- Create dashboard & panel: 10 min
- Configure query & chart: 5 min
- Add variables: 5 min
- Update .env & test: 10 min

