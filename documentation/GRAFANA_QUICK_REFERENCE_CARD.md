# GRAFANA PIE CHART - QUICK REFERENCE CARD

## IN 5 MINUTES

1. **Open Grafana**: http://localhost:3000 (admin/admin)
2. **Add MySQL**: Gear icon → Data Sources → MySQL Attendance
3. **Create Dashboard**: + icon → Dashboard → Add panel
4. **Add Pie Chart**: Choose Pie Chart visualization
5. **Paste Query**: See "SQL QUERY" section below
6. **Save**: Get Dashboard UID from URL

---

## SQL QUERY (COPY-PASTE)

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

## MYSQL DATASOURCE CONFIG

```
Name:       MySQL Attendance
Host:       mysql:3306
Database:   staffAttend_data
User:       root
Password:   root
```

---

## UPDATE .env

```
GRAFANA_DASHBOARD_UID=your_uid_from_grafana_url
GRAFANA_PIE_CHART_PANEL_ID=1
```

Then run:
```powershell
php artisan config:clear
php artisan cache:clear
```

---

## TEST IN BROWSER

```
http://localhost:8000/login
(Login with staff credentials)
→ Dashboard should show pie chart
```

---

## COLOR MAPPING

```
Present:    Green (#22c55e)
Absent:     Red (#ef4444)
Late:       Yellow (#eab308)
Leave:      Blue (#3b82f6)
EL:         Orange (#f97316)
HalfDay:    Purple (#a855f7)
```

---

## URLS

```
Grafana:            http://localhost:3000
phpMyAdmin:         http://localhost:8081
Laravel Dashboard:  http://localhost:8000
```

---

## MYSQL QUERY TEST

Go to phpMyAdmin (localhost:8081), select staffAttend_data, SQL tab, paste:

```sql
SELECT status, COUNT(*) as count
FROM attendance
WHERE staff_id = 1
AND YEAR(attendance_date) = 2025
AND MONTH(attendance_date) = 12
GROUP BY status;
```

(Replace 1 with your staff_id, adjust year/month)

---

## TROUBLESHOOTING

| Issue | Fix |
|-------|-----|
| No data in pie chart | Test query in phpMyAdmin first |
| Connection failed | Use host mysql:3306, not localhost |
| Variable not working | Refresh page, check variable query |
| iframe not showing | Update .env, clear cache |

---

## GRAFANA DATASOURCE TEST

1. Data Sources → MySQL Attendance
2. Scroll to bottom
3. Click "Save & Test"
4. Should show ✅ "Database Connection OK"

---

## FILES TO READ

1. **Quick Setup** → GRAFANA_PIE_CHART_INTEGRATION_QUICK_START.md (10 min read)
2. **Full Guide** → GRAFANA_SETUP_COMPLETE_GUIDE.md (20 min read)
3. **SQL Queries** → GRAFANA_SQL_READY_TO_USE.md (5 min reference)
4. **Visual Guide** → GRAFANA_SETUP_VISUAL_GUIDE.md (15 min read)

---

## KEY POINTS

✓ Pie chart shows **current month only**  
✓ **Auto-updates** when you enter new month  
✓ **Auto-refreshes** every 30 seconds  
✓ Shows **all attendance statuses**  
✓ **Percentage calculations** included  
✓ **Color-coded** for quick understanding  
✓ **Responsive design** (works on mobile)  

---

## VARIABLES

If you want to select different staff members:

```
1. Dashboard Settings → Variables → Add variable
2. Name: staffId
3. Type: Query  
4. Query: SELECT DISTINCT staff_id FROM staff
5. Dropdown appears on dashboard
```

Then use in SQL:
```sql
WHERE staff_id = ${staffId}
```

---

## NEXT PANELS (Optional)

After pie chart works, add:

1. **Table Panel**:
```sql
SELECT attendance_date, status, check_in_time
FROM attendance
WHERE staff_id = $__myVar
ORDER BY attendance_date DESC LIMIT 30
```

2. **Stat Panel** (Just Present):
```sql
SELECT COUNT(*) as value
FROM attendance
WHERE staff_id = $__myVar
AND status = 'present'
AND MONTH(attendance_date) = MONTH(NOW())
```

3. **Trend Chart** (Last 12 months):
```sql
SELECT DATE_FORMAT(attendance_date, '%Y-%m') as month,
       COUNT(*) as count
FROM attendance
WHERE staff_id = $__myVar
GROUP BY month
ORDER BY month DESC
LIMIT 12
```

---

## LARAVEL CACHE COMMANDS

After updating .env:
```powershell
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

Or all at once:
```powershell
php artisan optimize:clear
```

---

## DOCKER COMMANDS

Check if services running:
```powershell
docker ps
```

Restart all services:
```powershell
docker-compose restart
```

View logs:
```powershell
docker logs grafana_staff
docker logs mysql_staff
```

---

## TIME ESTIMATES

```
MySQL datasource:   5 min
Create dashboard:   5 min
Add pie chart:      3 min
Paste query:        2 min
Map fields:         2 min
Save & configure:   3 min
Update .env:        2 min
Test:              2 min
───────────────────────
TOTAL:            24 minutes
```

(Add 10 min if adding variables)

---

## VERIFICATION CHECKLIST

Before testing in Laravel:

```
☑ MySQL datasource connection OK
☑ Dashboard created and saved
☑ Pie chart shows data in preview
☑ SQL query returns 3+ rows
☑ Status and Count fields mapped
☑ Dashboard has UID
☑ .env updated with UID and Panel ID
☑ Laravel cache cleared
```

---

## SUPPORT DOCS

Located in: `C:\Users\syami\Desktop\StaffAttendance_System\`

- GRAFANA_INTEGRATION_COMPLETE_SUMMARY.md ← Architecture
- GRAFANA_SETUP_COMPLETE_GUIDE.md ← Full steps
- GRAFANA_SETUP_VISUAL_GUIDE.md ← Visual walkthrough
- GRAFANA_SQL_READY_TO_USE.md ← Query reference
- GRAFANA_PIE_CHART_INTEGRATION_QUICK_START.md ← 11 steps
- GRAFANA_SQL_QUERIES.sql ← All 8 queries with docs

---

## DASHBOARD DISPLAY

Your dashboard will show:

```
┌─────────────────────────────────────┐
│    Monthly Attendance Breakdown      │
├──────────────────┬──────────────────┤
│                  │   Present: 15    │
│   PIE CHART      │   Absent: 2      │
│                  │   Late: 1        │
│                  │   Other: 2       │
└──────────────────┴──────────────────┘

Legend:
✓ Green = Present
✗ Red = Absent  
⏱ Yellow = Late
📅 Blue = On Leave
🟠 Orange = EL
🟣 Purple = Half Day

(Auto-refreshes every 30 seconds)
```

---

## COMMON MISTAKES

❌ Using "localhost" instead of "mysql"  
❌ Wrong port (3307 for MySQL, not 3306)  
❌ Wrong database name spelling  
❌ Forgetting to clear Laravel cache  
❌ Not saving dashboard before getting UID  
❌ Copying UID of wrong dashboard  
❌ Using $__myVar before creating variable  

✅ Use these instead!

---

## QUICK LINKS

- Grafana Login: http://localhost:3000/login
- Add Data Source: http://localhost:3000/datasources
- New Dashboard: http://localhost:3000/dashboard/new
- phpMyAdmin: http://localhost:8081
- Laravel: http://localhost:8000

---

Last Updated: December 11, 2025
Documentation Version: 2.0
Status: Complete & Tested ✓

