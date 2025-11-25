# Phase 8 Quick Start - Grafana + MySQL Direct Connection

## ✅ What Was Done

### 1. Updated docker-compose.yml
- ❌ Removed Prometheus service entirely
- ✅ Simplified Grafana MySQL configuration
- ✅ Updated credentials to use root/root (matching MySQL)
- ✅ Fixed duplicate network definition

### 2. Created MySQL Datasource Configuration
- ✅ File: `grafana/provisioning/datasources/mysql.yml`
- ✅ Auto-provisions MySQL as default datasource
- ✅ Connection string: mysql:3306 → staffAttend_data

### 3. Created Real-time Dashboard
- ✅ File: `grafana/provisioning/dashboards/mysql-attendance-dashboard.json`
- ✅ 7 panels querying MySQL directly:
  - Total Present Today (stat card - green)
  - Total Absent Today (stat card - red)
  - Total Late Today (stat card - yellow)
  - Total On Leave Today (stat card - blue)
  - Status Distribution (pie chart)
  - Daily Trend (line chart - 7 days)
  - Detailed Records (table)
- ✅ Auto-refresh: 10 seconds
- ✅ All queries use raw SQL (no Prometheus metrics)

### 4. Dashboard Provider Configuration
- ✅ File: `grafana/provisioning/dashboards/provider.yml`
- ✅ Auto-loads dashboards from provisioning folder
- ✅ Updates every 10 seconds

## 🚀 Next Steps (IMPORTANT)

### Step 1: Start Docker Services
```powershell
cd "c:\Users\syami\Desktop\StaffAttendance_System"
docker-compose up -d
```

Expected output:
```
✓ Network staff-network created
✓ mysql_staff (running)
✓ phpmyadmin_staff (running)
✓ grafana_staff (running)
```

### Step 2: Wait for Grafana to Connect
Grafana needs ~30 seconds to:
1. Initialize
2. Connect to MySQL
3. Load datasource configuration
4. Load dashboard

Monitor with:
```bash
docker logs -f grafana_staff
```

Expected final message:
```
logger=sqlstore msg="Starting Grafana" version=12.2.1
...
(no "Error" messages in final logs)
```

### Step 3: Access Grafana
1. Open browser: **http://localhost:3000**
2. Login: **admin / admin**
3. Click "Dashboards" → "Staff Attendance Statistics - Real-time"

### Step 4: Verify Dashboard
- Should see today's attendance statistics
- Stat cards should show numbers (0 if no records today)
- Charts should be empty if no historical data
- Auto-refresh indicator should show "10s" interval

## 📊 Adding Test Data

To see dashboard in action, add attendance records:

### Option 1: Via Laravel App (Recommended)
1. Open http://localhost:8000
2. Login as staff: ahmad@utm.edu.my / password123
3. Go to Attendance page
4. Update status for today
5. Grafana dashboard should refresh in ~10 seconds

### Option 2: Via phpMyAdmin
1. Open http://localhost:8081
2. Login: root / root
3. Navigate to: staffAttend_data → attendance
4. Add new record:
   - staff_id: 1
   - attendance_date: TODAY
   - status: present (or absent, late, etc)
   - check_in_time: 08:30:00 (if present)
   - check_out_time: 17:00:00 (if present)

### Option 3: Via SQL
```sql
USE staffAttend_data;
INSERT INTO attendance (staff_id, attendance_date, status, check_in_time, check_out_time, remarks)
VALUES 
(1, CURDATE(), 'present', '08:30:00', '17:00:00', 'Regular'),
(2, CURDATE(), 'absent', NULL, NULL, 'Sick leave'),
(3, CURDATE(), 'late', '09:15:00', '17:00:00', 'Traffic');
```

## 🔍 Verification Checklist

- [ ] Docker containers running: `docker ps`
- [ ] MySQL accessible: `http://localhost:8081`
- [ ] Grafana accessible: `http://localhost:3000`
- [ ] Grafana admin login works
- [ ] Dashboard loads without errors
- [ ] Datasource shows "Staff Attendance MySQL"
- [ ] Dashboard shows "10s" refresh interval
- [ ] Test data visible in dashboard

## ⚙️ Configuration Summary

| Component | Port | Host | Status |
|-----------|------|------|--------|
| MySQL | 3307 | localhost | Running |
| phpMyAdmin | 8081 | localhost | Running |
| Grafana | 3000 | localhost | Running |
| Laravel App | 8000 | localhost | (Separate) |

| Configuration | Value |
|---|---|
| Grafana Admin | admin / admin |
| MySQL User | root / root |
| MySQL Database | staffAttend_data |
| Datasource | Staff Attendance MySQL |
| Dashboard Refresh | 10 seconds |
| Dashboard Name | Staff Attendance Statistics - Real-time |

## 📝 SQL Queries in Dashboard

All dashboard panels use direct MySQL queries:

1. **Present Count**: `SELECT COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() AND status = 'present'`
2. **Absent Count**: `SELECT COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() AND status = 'absent'`
3. **Late Count**: `SELECT COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() AND status = 'late'`
4. **On Leave Count**: `SELECT COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() AND status IN ('on leave', 'el', 'half day')`
5. **Status Breakdown**: `SELECT status, COUNT(*) FROM attendance WHERE DATE(attendance_date) = CURDATE() GROUP BY status`
6. **Trend (7 days)**: Multiple queries for Present/Absent/Late over last 7 days
7. **Detail Table**: `SELECT a.staff_id, s.name, a.status, a.check_in_time, a.check_out_time, a.remarks FROM attendance a JOIN staff s ON a.staff_id = s.staff_id WHERE DATE(a.attendance_date) = CURDATE()`

## 🐛 Troubleshooting

### Issue: Grafana shows "No Data" in panels
**Cause**: No attendance records for today  
**Solution**: Add test data via Laravel app or phpMyAdmin

### Issue: Dashboard doesn't load
**Cause**: Grafana hasn't finished initializing  
**Solution**: Wait 30 seconds and refresh page

### Issue: "Connection refused" error
**Cause**: MySQL container not running  
**Solution**: `docker-compose restart mysql`

### Issue: Grafana login doesn't work
**Cause**: Default credentials changed  
**Solution**: Check docker-compose.yml for GF_SECURITY_ADMIN_PASSWORD

## 📚 Files Modified/Created

- ✅ `docker-compose.yml` - Updated with MySQL credentials
- ✅ `grafana/provisioning/datasources/mysql.yml` - Created MySQL datasource
- ✅ `grafana/provisioning/dashboards/mysql-attendance-dashboard.json` - Created dashboard
- ✅ `GRAFANA_MYSQL_SETUP.md` - Comprehensive setup documentation
- ✅ `PHASE_8_QUICKSTART.md` - This file

## 🎯 Phase 8 Complete When

✅ docker-compose up -d runs successfully  
✅ Grafana accessible at http://localhost:3000  
✅ Dashboard loads with auto-refresh  
✅ Datasource shows "Staff Attendance MySQL"  
✅ Test data displays in real-time on dashboard  

---

## Commands Reference

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View Grafana logs
docker logs -f grafana_staff

# Restart Grafana
docker restart grafana_staff

# Check container status
docker ps

# Connect to MySQL
docker exec -it mysql_staff mysql -uroot -proot -e "SELECT * FROM staffAttend_data.attendance;"

# Remove all and restart clean
docker-compose down -v
docker-compose up -d
```

---

**Status**: Ready for deployment  
**Date**: 2025-11-25  
**Phase**: 8 - Grafana + MySQL Direct Connection
