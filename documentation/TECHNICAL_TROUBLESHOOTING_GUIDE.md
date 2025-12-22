# Staff Dashboard - Technical Troubleshooting Guide

## Quick Diagnostics

### Step 1: Check Server Status
```powershell
# Check if containers are running
docker ps

# Expected output should show:
# - grafana container (port 3000)
# - mysql container (port 3307)
```

### Step 2: Test Grafana Connection
```powershell
# Open browser and visit
http://localhost:3000

# Should see Grafana login page
# Login: admin / admin
```

### Step 3: Test Laravel Application
```powershell
# In staff_attendance directory
php artisan serve

# Visit dashboard
http://localhost:8000/staff/dashboard
```

---

## Common Issues & Solutions

### Issue 1: Dashboard Shows Blank White Area Where Pie Chart Should Be

**Symptoms:**
- White/empty space instead of pie chart
- No error message in browser console
- Other dashboard elements display normally

**Diagnosis Steps:**
```javascript
// Open browser DevTools (F12) → Console
// Check if iframe exists:
document.getElementById('grafanaChart')
// Should return: <iframe id="grafanaChart" ...>

// Check iframe src:
document.getElementById('grafanaChart').src
// Should return: http://localhost:3000/d-solo/adtx5zp/...
```

**Solutions:**

**A. Grafana Not Running**
```powershell
# Start Grafana
docker-compose up -d grafana

# Wait 10-15 seconds for startup
# Test: http://localhost:3000
```

**B. Dashboard Doesn't Exist**
```
Steps:
1. Go to http://localhost:3000
2. Login with admin/admin
3. Go to Dashboards
4. Search for "attendance-dashboard"
5. If not found, create new dashboard
6. Add pie chart panel
7. Name dashboard exactly: "attendance-dashboard"
8. Note the dashboard UID (in URL)
9. Update staff_dashboard.blade.php line 191:
   src="http://localhost:3000/d-solo/[YOUR-UID]/attendance-dashboard?..."
```

**C. MySQL Data Source Not Connected**
```
Steps:
1. Go to Grafana → Settings → Data Sources
2. Click "Add data source"
3. Select MySQL
4. Configure:
   - Host: mysql:3306 (or localhost:3307)
   - Database: staffAttend_data
   - User: root
   - Password: [your password]
5. Test connection
6. Save
7. Go back to dashboard
8. Edit pie chart panel
9. Select MySQL data source
10. Configure SQL query
```

---

### Issue 2: "ERR_CONNECTION_REFUSED" or "Failed to Connect"

**Symptoms:**
- Browser console shows connection error
- Iframe shows error message
- "Cannot connect to http://localhost:3000"

**Causes & Solutions:**

**A. Grafana Port Conflict**
```powershell
# Check what's using port 3000
netstat -ano | findstr :3000

# Kill process if needed
taskkill /PID [PID] /F

# Restart Grafana
docker-compose restart grafana
```

**B. Grafana Container Crashed**
```powershell
# Check container status
docker-compose logs grafana

# Restart
docker-compose restart grafana

# View logs for errors
docker-compose logs -f grafana
```

**C. Wrong Host Address**
```
If accessing from different machine:
- Change localhost:3000 → [your-ip]:3000
- Example: http://192.168.1.100:3000
```

---

### Issue 3: "No Data" or "Query Returned Empty"

**Symptoms:**
- Pie chart renders but shows no data
- MySQL connection works
- Dashboard loads without errors

**Diagnosis:**

**A. Check MySQL Data**
```bash
# SSH into MySQL container
docker exec -it [mysql_container_id] mysql -u root -p

# Run query
USE staffAttend_data;
SELECT COUNT(*) as total FROM attendance;

# Should show: total | [number > 0]
```

**B. Check Date Range**
```javascript
// Browser console - check selected month
document.getElementById('monthSelector').value
// Should return: 2025-12

// Check calculated timestamps
const selectedMonth = '2025-12';
const [year, month] = selectedMonth.split('-');
const firstDay = new Date(year, month - 1, 1);
const lastDay = new Date(year, month, 0);
console.log(firstDay.getTime(), lastDay.getTime());
```

**C. Verify Grafana SQL Query**
```
Steps:
1. In Grafana dashboard
2. Click pie chart panel
3. Click "Edit"
4. Check SQL query in query editor
5. Should look like:
   SELECT status, COUNT(*) as count 
   FROM attendance 
   WHERE attendance_date BETWEEN ... 
   GROUP BY status
6. Click "Run Query" button
7. Should show results in table below
```

---

### Issue 4: Month Selector Doesn't Work

**Symptoms:**
- Dropdown shows months but chart doesn't update
- Chart stays same even after selecting different month
- No error in browser console

**Diagnosis:**
```javascript
// Browser console
// Test if function exists:
typeof updateGrafanaChart
// Should return: "function"

// Test if selector exists:
document.getElementById('monthSelector')
// Should return: <select id="monthSelector">

// Manually trigger update:
updateGrafanaChart()
// Should see chart reload
```

**Solutions:**

**A. JavaScript Error in File**
```powershell
# Check browser console (F12 → Console tab)
# Look for red error messages
# Common errors:
# - Unexpected token (syntax error)
# - Cannot read property (undefined variable)
# - Function not defined
```

**B. Fix JavaScript Syntax**
```
File: staff_dashboard.blade.php
Lines: 375-420

Common issues to check:
✗ Line 375: selectedMonth. split ← Remove spaces
✗ Line 379: Math. floor ← Remove spaces
✗ Line 385: grafanaChart. src ← Remove spaces
```

---

### Issue 5: Auto-Refresh Not Working

**Symptoms:**
- "Last Updated" timestamp doesn't change
- Chart doesn't refresh every 30 seconds
- Manual refresh button works fine

**Diagnosis:**
```javascript
// Browser console
// Check if interval is set:
// (Open DevTools, check Sources → Timers)

// Manually test refresh:
refreshGrafanaChart()
// Chart should reload
```

**Solutions:**

**A. Check Interval Code**
```
File: staff_dashboard.blade.php
Line: 413

Should contain:
setInterval(() => {
  refreshGrafanaChart();
}, 30000);

If missing, add before closing </script> tag
```

**B. Page Not Loaded Properly**
```javascript
// Check if DOMContentLoaded ran:
// Browser console:
console.log('Document ready:', document.readyState);

// Should show: "Document ready: complete"
```

---

### Issue 6: Chart Shows Wrong Data or Overlapping iframes

**Symptoms:**
- Pie chart displays but with incorrect numbers
- Chart looks garbled or overlapped
- Multiple iframes visible on page

**Causes & Solutions:**

**A. Duplicate iframe Code**
```
File: staff_dashboard.blade.php
Line: 191

Check for:
✗ <iframe src="<iframe src="..."></iframe>"> (nested)
✓ <iframe src="http://..."> (single)

If nested found, fix by removing outer tags
```

**B. Incorrect Panel ID**
```
Grafana Dashboard Settings:
1. Go to dashboard
2. Click pie chart panel
3. Panel ID should be in URL: panelId=1
4. Update staff_dashboard.blade.php line 407:
   panelId=1 (match your panel ID)
```

**C. Stale Cache**
```powershell
# Hard refresh browser (bypasses cache)
Ctrl + Shift + R  (Windows/Linux)
Cmd + Shift + R   (Mac)

# Or clear browser cache manually
Settings → Clear Browsing Data → Cache
```

---

## Browser Console Debugging

### Enable Console
1. Press `F12` to open DevTools
2. Click "Console" tab
3. Type commands to test

### Useful Commands

**Check if Grafana is reachable:**
```javascript
fetch('http://localhost:3000/api/health')
  .then(r => r.json())
  .then(d => console.log('Grafana OK:', d))
  .catch(e => console.log('Grafana error:', e.message))
```

**Check iframe URL:**
```javascript
document.getElementById('grafanaChart').src
```

**Manually test month update:**
```javascript
document.getElementById('monthSelector').value = '2025-11';
updateGrafanaChart();
```

**Check all chart elements:**
```javascript
console.log('Chart exists:', !!document.getElementById('grafanaChart'));
console.log('Selector exists:', !!document.getElementById('monthSelector'));
console.log('Timestamp exists:', !!document.getElementById('lastUpdateTime'));
console.log('Functions available:', {
  updateGrafanaChart: typeof updateGrafanaChart,
  refreshGrafanaChart: typeof refreshGrafanaChart,
  updateLastRefreshTime: typeof updateLastRefreshTime
});
```

---

## Docker Commands for Debugging

### View Logs
```powershell
# Grafana logs
docker-compose logs -f grafana

# MySQL logs
docker-compose logs -f mysql

# All services
docker-compose logs -f

# Last 50 lines
docker-compose logs --tail=50
```

### Access Containers
```powershell
# Access MySQL
docker exec -it staffattendance_system-mysql-1 mysql -u root -p

# Access Grafana CLI
docker exec -it staffattendance_system-grafana-1 grafana-cli admin list-users

# View container filesystem
docker exec -it [container_id] ls -la /var/lib/grafana
```

### Container Health Check
```powershell
# Check container status
docker-compose ps

# Inspect container
docker inspect [container_id]

# Restart single service
docker-compose restart grafana
```

---

## Network Diagnostics

### Test Port Connectivity
```powershell
# Test Grafana port
Test-NetConnection localhost -Port 3000

# Test MySQL port
Test-NetConnection localhost -Port 3307

# Test Laravel port
Test-NetConnection localhost -Port 8000
```

### DNS Resolution (if using hostnames)
```powershell
# Test hostname
[System.Net.Dns]::GetHostAddresses("mysql")
[System.Net.Dns]::GetHostAddresses("grafana")
```

---

## Database Diagnostics

### Check Attendance Data
```sql
-- Count total records
SELECT COUNT(*) as total_records FROM attendance;

-- Count by status
SELECT status, COUNT(*) as count 
FROM attendance 
GROUP BY status;

-- Check date range
SELECT MIN(attendance_date) as earliest, 
       MAX(attendance_date) as latest 
FROM attendance;

-- Check specific month
SELECT status, COUNT(*) as count 
FROM attendance 
WHERE YEAR(attendance_date) = 2025 
  AND MONTH(attendance_date) = 12 
GROUP BY status;
```

### Optimize Queries
```sql
-- Create index for better performance
CREATE INDEX idx_attendance_date ON attendance(attendance_date);
CREATE INDEX idx_attendance_status ON attendance(status);
```

---

## Performance Optimization

### Reduce Auto-Refresh Frequency
```javascript
// Current: 30 seconds
// Change to: 60 seconds
setInterval(() => {
  refreshGrafanaChart();
}, 60000);  // Changed from 30000
```

### Optimize MySQL Query
```sql
-- Add WHERE clause to limit data
SELECT status, COUNT(*) as count 
FROM attendance 
WHERE attendance_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
GROUP BY status;
```

### Compress Response
```
In Grafana: Administration → General → Compression
Enable gzip compression for responses
```

---

## Production Checklist

- [ ] Grafana running with persistent storage
- [ ] MySQL backups configured
- [ ] HTTPS enabled (not just HTTP)
- [ ] Database credentials in environment variables
- [ ] Firewall rules configured
- [ ] Monitoring/alerting set up
- [ ] Dashboard auto-refresh optimized
- [ ] Database indexes created
- [ ] Error logging configured
- [ ] User authentication enabled

---

## Support Resources

### Documentation Links
- **Grafana Docs:** https://grafana.com/docs/
- **Laravel Docs:** https://laravel.com/docs/
- **MySQL Docs:** https://dev.mysql.com/doc/
- **Tailwind CSS:** https://tailwindcss.com/docs

### Common Commands Reference

```powershell
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Restart specific service
docker-compose restart grafana

# View logs
docker-compose logs -f grafana

# Access MySQL
docker exec -it [container] mysql -u root -p

# Run Laravel command
docker exec -it [container] php artisan migrate

# Clear Laravel cache
docker exec -it [container] php artisan cache:clear
```

---

**Last Updated:** December 10, 2025  
**Version:** 1.0  
**Status:** Production Ready
