# Phase 8 Deployment Checklist

## Pre-Deployment

- [ ] Docker Desktop is running
- [ ] Terminal is open in: `c:\Users\syami\Desktop\StaffAttendance_System`
- [ ] Have read `PHASE_8_QUICKSTART.md`
- [ ] Port 3307 (MySQL) is not in use
- [ ] Port 3000 (Grafana) is not in use
- [ ] Port 8081 (phpMyAdmin) is not in use

## Deployment Steps

### Step 1: Verify Configuration
- [ ] `docker-compose.yml` exists and updated
- [ ] MySQL datasource: `grafana/provisioning/datasources/mysql.yml` ✓
- [ ] Dashboard: `grafana/provisioning/dashboards/mysql-attendance-dashboard.json` ✓
- [ ] Provider: `grafana/provisioning/dashboards/provider.yml` ✓

### Step 2: Start Services
```powershell
cd "c:\Users\syami\Desktop\StaffAttendance_System"
docker-compose up -d
```

**Expected Output**:
```
[+] Running 3/3
 ✔ Network staff-network Created
 ✔ Container mysql_staff Started
 ✔ Container phpmyadmin_staff Started
 ✔ Container grafana_staff Started
```

- [ ] Command executed successfully
- [ ] No error messages
- [ ] All 3 containers started

### Step 3: Wait for Initialization
```bash
docker logs -f grafana_staff
```

**Wait for message**:
```
logger=sqlstore msg="Starting Grafana"
(look for absence of "Error" messages at end)
```

- [ ] Grafana initialized (takes 20-30 seconds)
- [ ] No "Error" messages in final logs
- [ ] Press Ctrl+C to exit

### Step 4: Verify Services Are Running
```bash
docker ps
```

**Should show**:
- ✓ mysql_staff (Up)
- ✓ phpmyadmin_staff (Up)
- ✓ grafana_staff (Up)

- [ ] All 3 containers show "Up" status
- [ ] No containers show "Exited"

### Step 5: Verify MySQL Connection
```bash
docker exec mysql_staff mysql -uroot -proot -e "SELECT DATABASE();" 2>&1
```

**Expected Output**:
```
DATABASE()
staffAttend_data
```

- [ ] MySQL responds successfully
- [ ] Database name shown correctly

### Step 6: Access Grafana Web UI
1. Open browser: **http://localhost:3000**
2. Should see Grafana login page

- [ ] Grafana web UI accessible
- [ ] Login page displayed

### Step 7: Login to Grafana
1. Username: `admin`
2. Password: `admin`
3. Click "Sign in"

- [ ] Login successful
- [ ] No error messages
- [ ] Redirected to home dashboard

### Step 8: Verify Datasource
1. Click **Administration** (left menu)
2. Click **Data Sources**
3. Should see **"Staff Attendance MySQL"**

- [ ] Datasource listed
- [ ] Status shows "Connected" or green checkmark
- [ ] Type shows "MySQL"

### Step 9: Test Datasource Connection
1. Click on **"Staff Attendance MySQL"** datasource
2. Scroll to bottom
3. Click **"Save & Test"**
4. Should show **"Database connection successful"**

- [ ] Connection test successful
- [ ] Green success message shown
- [ ] No error messages

### Step 10: View Dashboard
1. Click **Dashboards** (left menu)
2. Should see **"Staff Attendance Statistics - Real-time"**
3. Click on dashboard name

- [ ] Dashboard listed
- [ ] Dashboard loads
- [ ] All 7 panels visible

### Step 11: Verify Dashboard Panels
Check that all 7 panels are visible:
- [ ] Panel 1: "Total Present Today" (stat card)
- [ ] Panel 2: "Total Absent Today" (stat card)
- [ ] Panel 3: "Total Late Today" (stat card)
- [ ] Panel 4: "Total On Leave Today" (stat card)
- [ ] Panel 5: "Attendance Status Distribution" (pie chart)
- [ ] Panel 6: "Daily Attendance Trend" (line chart)
- [ ] Panel 7: "Detailed Attendance Records" (table)

### Step 12: Verify Auto-Refresh
1. Look at top-right of dashboard
2. Should show **"10s"** or similar refresh interval
3. Click the refresh icon - should update panels

- [ ] Refresh interval shown
- [ ] Dashboard auto-refreshes every 10 seconds
- [ ] Refresh icon clickable

### Step 13: Access phpMyAdmin
1. Open browser: **http://localhost:8081**
2. Server: `mysql`
3. User: `root`
4. Password: `root`
5. Click "Go"

- [ ] phpMyAdmin loads
- [ ] Login successful
- [ ] Database "staffAttend_data" visible in left menu

### Step 14: Verify Attendance Table
1. In phpMyAdmin, expand **staffAttend_data**
2. Click on **attendance** table
3. Click **Browse** tab

- [ ] Table accessible
- [ ] Shows table structure and any existing records
- [ ] Columns: staff_id, attendance_date, status, check_in_time, check_out_time, remarks, created_at

### Step 15: Add Test Data (Optional)
1. Click **Insert** tab in phpMyAdmin
2. Add test record:
   - staff_id: `1`
   - attendance_date: `TODAY` (current date)
   - status: `present`
   - check_in_time: `08:30:00`
   - check_out_time: `17:00:00`
3. Click **Go**

- [ ] Record inserted successfully
- [ ] No error messages

### Step 16: Verify Dashboard Shows Test Data
1. Go back to Grafana: **http://localhost:3000**
2. Navigate to dashboard
3. Panels should update with data

- [ ] Dashboard shows test data
- [ ] Stat card "Total Present Today" shows count
- [ ] Wait 10 seconds for auto-refresh confirmation

## Post-Deployment Verification

### Database Connectivity
- [ ] MySQL container running: `docker ps | grep mysql_staff`
- [ ] Database accessible via phpMyAdmin: `http://localhost:8081`
- [ ] Grafana datasource connected: Administration → Data Sources

### Dashboard Functionality
- [ ] All 7 panels visible
- [ ] Panels show data or empty state
- [ ] Auto-refresh working (10 seconds)
- [ ] Table shows all columns: staff_id, name, status, check_in, check_out, remarks

### Data Consistency
- [ ] Attendance records in MySQL visible in dashboard
- [ ] New records appear on dashboard after ~10 seconds
- [ ] Data matches between phpMyAdmin and Grafana

## Troubleshooting During Deployment

### Issue: "Connection refused" on Docker commands
**Solution**: Start Docker Desktop

### Issue: "Port already in use" error
**Solution**: 
```bash
# Stop conflicting service
docker-compose down
# Or change ports in docker-compose.yml
```

### Issue: Grafana won't start
**Solution**:
```bash
# Check logs
docker logs grafana_staff

# Restart
docker restart grafana_staff
```

### Issue: "Access denied" from Grafana to MySQL
**Solution**: Verify docker-compose.yml has:
```yaml
- GF_DATABASE_USER=root
- GF_DATABASE_PASSWORD=root
```

### Issue: Dashboard shows "No Data"
**Solution**: 
1. Check MySQL has records: `http://localhost:8081`
2. Check datasource connection: Administration → Data Sources
3. Verify attendance_date is TODAY

## Success Criteria (All Must Be ✓)

- [ ] 3 containers running (mysql, grafana, phpmyadmin)
- [ ] Grafana accessible at http://localhost:3000
- [ ] Login works (admin/admin)
- [ ] Datasource "Staff Attendance MySQL" connected
- [ ] Dashboard loads: "Staff Attendance Statistics - Real-time"
- [ ] All 7 panels visible on dashboard
- [ ] Auto-refresh shows 10 seconds
- [ ] Test data displays on dashboard
- [ ] phpMyAdmin accessible at http://localhost:8081
- [ ] Attendance table viewable in phpMyAdmin

## Post-Deployment Commands

### Daily Start
```powershell
cd "c:\Users\syami\Desktop\StaffAttendance_System"
docker-compose up -d
```

### Daily Stop
```bash
docker-compose down
```

### View Logs
```bash
docker logs -f grafana_staff
```

### Restart Grafana
```bash
docker restart grafana_staff
```

### Full Restart (Remove all data)
```bash
docker-compose down -v
docker-compose up -d
```

### Access Points After Deployment
| Service | URL | Credentials |
|---------|-----|-------------|
| Grafana | http://localhost:3000 | admin / admin |
| phpMyAdmin | http://localhost:8081 | root / root |
| Laravel App | http://localhost:8000 | (separate) |
| MySQL | localhost:3307 | root / root |

## Documentation Files

- 📖 `GRAFANA_MYSQL_SETUP.md` - Full technical documentation
- 🚀 `PHASE_8_QUICKSTART.md` - Quick start guide
- ✅ `PHASE_8_COMPLETION_SUMMARY.md` - What was delivered
- ✓ `PHASE_8_DEPLOYMENT_CHECKLIST.md` - This file

## Sign-Off

- [ ] All steps completed
- [ ] All success criteria met
- [ ] Dashboard fully functional
- [ ] Ready for production use
- [ ] Documentation reviewed

**Deployment Completed**: ___________________ (Date)  
**Verified By**: ___________________ (Name)  
**Notes**: ___________________

---

**Status**: Ready for Deployment  
**Version**: Phase 8 - Grafana + MySQL Direct Connection  
**Last Updated**: 2025-11-25
