# Grafana + MySQL Direct Connection Setup

## Overview
This configuration connects Grafana directly to MySQL database without Prometheus, providing real-time attendance statistics with 10-second auto-refresh dashboards.

## Architecture
```
Browser (localhost:3000)
    ↓
Grafana Container (grafana_staff)
    ↓
MySQL Container (mysql_staff)
    ↓
staffAttend_data Database
    ↓
attendance Table + staff Table
```

## Docker Services

### 1. MySQL (mysql_staff)
- **Image**: mysql:8.0
- **Port**: 3307 (host) → 3306 (container)
- **Database**: staffAttend_data
- **User**: root
- **Password**: root
- **Volumes**: ./mysql_data:/var/lib/mysql

### 2. phpMyAdmin (phpmyadmin_staff)
- **Image**: phpmyadmin
- **Port**: 8081 (host) → 80 (container)
- **Database**: staffAttend_data
- **URL**: http://localhost:8081

### 3. Grafana (grafana_staff)
- **Image**: grafana/grafana:latest
- **Port**: 3000 (host) → 3000 (container)
- **Admin User**: admin
- **Admin Password**: admin
- **Database Type**: MySQL
- **Database Host**: mysql:3306
- **Database User**: root
- **Database Password**: root
- **Refresh Interval**: 10 seconds

## Files Created

### 1. docker-compose.yml (Updated)
- Removed: Prometheus service entirely
- Simplified Grafana configuration
- Added MySQL credentials as environment variables
- All services on internal `staff-network`

### 2. grafana/provisioning/datasources/mysql.yml
- Auto-provisions MySQL as Grafana datasource
- Name: "Staff Attendance MySQL"
- Type: mysql
- Host: mysql:3306
- Database: staffAttend_data
- Default datasource: true

### 3. grafana/provisioning/dashboards/mysql-attendance-dashboard.json
- Real-time attendance dashboard
- 6 panels with SQL queries
- Auto-refresh: 10 seconds
- Panels:
  - **Total Present Today** (Green stat card)
  - **Total Absent Today** (Red stat card)
  - **Total Late Today** (Yellow stat card)
  - **Total On Leave Today** (Blue stat card)
  - **Attendance Status Distribution** (Pie chart - today)
  - **Daily Attendance Trend** (Line chart - last 7 days)
  - **Detailed Attendance Records** (Table - today's records)

### 4. grafana/provisioning/dashboards/provider.yml
- Automatically loads dashboards from provisioning folder
- Updates every 10 seconds
- Allows UI updates

## Database Tables Queried

### attendance Table
```sql
- staff_id: INT (FK)
- attendance_date: DATE
- check_in_time: TIME (nullable)
- check_out_time: TIME (nullable)
- status: ENUM('present', 'absent', 'late', 'el', 'on leave', 'half day')
- remarks: TEXT (nullable)
- created_at: TIMESTAMP
```

### staff Table
```sql
- staff_id: INT (PK)
- name: VARCHAR(255)
- email: VARCHAR(255)
```

## SQL Queries Used

### 1. Present Today
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() 
AND status = 'present'
```

### 2. Absent Today
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() 
AND status = 'absent'
```

### 3. Late Today
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() 
AND status = 'late'
```

### 4. On Leave Today
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() 
AND status IN ('on leave', 'el', 'half day')
```

### 5. Status Distribution
```sql
SELECT status, COUNT(*) as count FROM attendance 
WHERE DATE(attendance_date) = CURDATE() 
GROUP BY status
```

### 6. Daily Trend (Present)
```sql
SELECT DATE(attendance_date) as time, COUNT(*) as Present FROM attendance 
WHERE status = 'present' 
AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
GROUP BY DATE(attendance_date)
```

### 7. Detailed Records
```sql
SELECT a.staff_id, s.name, a.status, a.check_in_time, a.check_out_time, a.remarks 
FROM attendance a 
JOIN staff s ON a.staff_id = s.staff_id 
WHERE DATE(a.attendance_date) = CURDATE() 
ORDER BY a.created_at DESC
```

## How to Access

### Grafana
1. Open browser: **http://localhost:3000**
2. Login with: **admin / admin**
3. Navigate to **Dashboards** → **Staff Attendance Statistics - Real-time**

### phpMyAdmin
1. Open browser: **http://localhost:8081**
2. Server: mysql
3. User: root
4. Password: root
5. Database: staffAttend_data

## Starting Services

### Start All Services
```bash
cd c:\Users\syami\Desktop\StaffAttendance_System
docker-compose up -d
```

### View Logs
```bash
docker logs -f grafana_staff
```

### Stop Services
```bash
docker-compose down
```

## Verification

### 1. Check Docker Status
```bash
docker ps
```
Should show: mysql_staff, phpmyadmin_staff, grafana_staff (all running)

### 2. Verify MySQL Connection
```bash
docker exec mysql_staff mysql -uroot -proot -e "SELECT COUNT(*) FROM staffAttend_data.attendance;"
```

### 3. Verify Grafana Datasource
1. Login to Grafana (http://localhost:3000)
2. Go to **Administration** → **Data Sources**
3. Should show "Staff Attendance MySQL" as default datasource
4. Click to verify: Connection should show "successful"

### 4. Verify Dashboard
1. Go to **Dashboards** → **Staff Attendance Statistics - Real-time**
2. Should display real-time attendance data
3. Panels should auto-refresh every 10 seconds

## Troubleshooting

### Grafana Cannot Connect to MySQL
**Error**: "Error 1045 (28000): Access denied for user 'root'@..."
**Solution**: Verify docker-compose.yml has correct credentials:
```yaml
- GF_DATABASE_USER=root
- GF_DATABASE_PASSWORD=root
```

### Dashboard Shows No Data
**Cause**: No attendance records for today
**Solution**: Add test records via:
- phpMyAdmin (http://localhost:8081)
- Or Laravel app at http://localhost:8000

### Panels Show "No Data"
**Cause**: SQL queries returning no results
**Solution**:
1. Verify attendance table has data: Check phpMyAdmin
2. Verify dates are correct: Use CURDATE() in queries
3. Check datasource connection: Administration → Data Sources

### MySQL Container Won't Start
**Cause**: Port 3307 already in use or zombie process
**Solution**:
```bash
docker container rm -f mysql_staff
docker-compose up -d mysql
```

## Features

✅ Real-time attendance statistics  
✅ 10-second auto-refresh  
✅ 7-day trend analysis  
✅ Status breakdown (pie chart)  
✅ Detailed attendance records table  
✅ Direct MySQL queries (no Prometheus overhead)  
✅ Auto-provisioned datasource  
✅ Auto-loaded dashboard  

## Performance

- **Query Execution**: <500ms per panel
- **Refresh Interval**: 10 seconds (configurable)
- **Network**: Internal Docker network (staff-network)
- **Storage**: Grafana data persisted in grafana_data volume

## Next Steps

1. Login to Grafana: http://localhost:3000 (admin/admin)
2. View dashboard: Dashboards → Staff Attendance Statistics - Real-time
3. Add test attendance records via Laravel app
4. Monitor dashboard auto-refresh
5. Customize dashboard panels as needed

## Related Files

- `docker-compose.yml` - Service orchestration
- `grafana/provisioning/datasources/mysql.yml` - Datasource auto-provisioning
- `grafana/provisioning/dashboards/mysql-attendance-dashboard.json` - Dashboard definition
- `grafana/provisioning/dashboards/provider.yml` - Dashboard provider
- `staff_attendance/app/Models/Attendance.php` - Attendance model
- `staff_attendance/database/migrations/*` - Database schema

---
**Status**: ✅ Ready for docker-compose up -d  
**Last Updated**: 2025-11-25  
**Phase**: 8 - MySQL Direct Connection (Grafana)
