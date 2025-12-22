# Grafana MySQL Connection Setup Guide

## Overview
Connect Grafana directly to your MySQL database to visualize leave balance, attendance data, and other metrics.

## Docker Setup

Your docker-compose.yml is already configured with:
- ✅ MySQL (port 3307)
- ✅ phpMyAdmin (port 8081)
- ✅ Grafana (port 3000)
- ✅ Prometheus (port 9090) - Optional

All containers are on the same network: `staff-network`

## Step 1: Restart Containers

```bash
cd c:\Users\syami\Desktop\StaffAttendance_System

# Stop existing containers
docker-compose down

# Start containers with MySQL driver plugin
docker-compose up -d
```

Wait for all containers to be healthy (2-3 minutes).

## Step 2: Add MySQL Data Source in Grafana

1. **Open Grafana**: Go to `http://localhost:3000`
2. **Login**: 
   - Username: `admin`
   - Password: `admin`

3. **Add Data Source**:
   - Click the **Settings** icon (gear) in the sidebar
   - Select **Data Sources**
   - Click **Add data source**
   - Choose **MySQL**

4. **Configure Connection**:
   - **Name**: `StaffAttendance-MySQL`
   - **Host**: `mysql:3306` (use service name for Docker)
   - **Database**: `staffAttend_data`
   - **Username**: `root`
   - **Password**: `root`
   - **SSL Mode**: `disable`

5. **Test Connection**: Click "Save & Test"
   - Should show ✅ "Database Connection OK"

## Step 3: Create Leave Balance Dashboard

### Create New Dashboard

1. Click **Create** → **Dashboard**
2. Click **Add new panel**

### Panel 1: Total Annual Leave Balance

**Query:**
```sql
SELECT 
    SUM(20) as total_balance,
    DATE_FORMAT(NOW(), '%Y-%m-%d') as date
FROM staff s
```

**Panel Settings:**
- Type: **Stat**
- Title: "Total Annual Leave Balance"
- Unit: "short"
- Color: **Blue**

### Panel 2: Total Leave Used

**Query:**
```sql
SELECT 
    COUNT(DISTINCT lr.staff_id) as staff_with_leave,
    SUM(DATEDIFF(lr.to_date, lr.from_date) + 1) as total_days_used
FROM leave_requests lr
WHERE lr.status = 'approved'
AND lr.leave_type = 'Annual Leave'
AND YEAR(lr.from_date) = YEAR(NOW())
```

**Panel Settings:**
- Type: **Stat**
- Title: "Total Leave Used (This Year)"
- Unit: "short"
- Color: **Orange**

### Panel 3: Total Leave Remaining

**Query:**
```sql
SELECT 
    (SELECT SUM(20) FROM staff) - 
    SUM(DATEDIFF(lr.to_date, lr.from_date) + 1) as remaining_balance
FROM leave_requests lr
WHERE lr.status = 'approved'
AND lr.leave_type = 'Annual Leave'
AND YEAR(lr.from_date) = YEAR(NOW())
```

**Panel Settings:**
- Type: **Stat**
- Title: "Total Leave Remaining"
- Unit: "short"
- Color: **Green**

### Panel 4: Leave Requests Status (Pie Chart)

**Query:**
```sql
SELECT 
    status,
    COUNT(*) as count
FROM leave_requests
GROUP BY status
```

**Panel Settings:**
- Type: **Pie Chart**
- Title: "Leave Requests by Status"
- Legend: **Table**

### Panel 5: Attendance Overview (Last 7 Days)

**Query:**
```sql
SELECT 
    attendance_date as Date,
    status,
    COUNT(*) as count
FROM attendance
WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY attendance_date, status
ORDER BY attendance_date DESC
```

**Panel Settings:**
- Type: **Time Series**
- Title: "Attendance Status (Last 7 Days)"

### Panel 6: Staff Leave Balance Individual

**Query:**
```sql
SELECT 
    s.staff_id,
    s.staff_name,
    20 as total_leave,
    COALESCE(SUM(DATEDIFF(lr.to_date, lr.from_date) + 1), 0) as used_leave,
    20 - COALESCE(SUM(DATEDIFF(lr.to_date, lr.from_date) + 1), 0) as remaining
FROM staff s
LEFT JOIN leave_requests lr ON s.staff_id = lr.staff_id 
    AND lr.status = 'approved' 
    AND lr.leave_type = 'Annual Leave'
    AND YEAR(lr.from_date) = YEAR(NOW())
GROUP BY s.staff_id, s.staff_name
ORDER BY s.staff_name
```

**Panel Settings:**
- Type: **Table**
- Title: "Individual Staff Leave Balance"

### Panel 7: Pending Leave Requests

**Query:**
```sql
SELECT 
    lr.leave_request_id,
    s.staff_name,
    lr.leave_type,
    lr.from_date as Start,
    lr.to_date as End,
    DATEDIFF(lr.to_date, lr.from_date) + 1 as Days,
    lr.created_at as Requested
FROM leave_requests lr
JOIN staff s ON lr.staff_id = s.staff_id
WHERE lr.status = 'pending'
ORDER BY lr.created_at DESC
```

**Panel Settings:**
- Type: **Table**
- Title: "Pending Leave Requests"

### Panel 8: Monthly Leave Summary

**Query:**
```sql
SELECT 
    DATE_FORMAT(lr.from_date, '%Y-%m') as Month,
    COUNT(*) as total_requests,
    SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected
FROM leave_requests lr
GROUP BY DATE_FORMAT(lr.from_date, '%Y-%m')
ORDER BY Month DESC
LIMIT 12
```

**Panel Settings:**
- Type: **Table**
- Title: "Monthly Leave Summary"

## Step 4: Create Attendance Dashboard

### Panel 1: Today's Attendance Summary

**Query:**
```sql
SELECT 
    status,
    COUNT(*) as count
FROM attendance
WHERE attendance_date = CURDATE()
GROUP BY status
```

**Panel Settings:**
- Type: **Pie Chart**
- Title: "Today's Attendance"

### Panel 2: Attendance by Status (Last 30 Days)

**Query:**
```sql
SELECT 
    attendance_date as Date,
    status,
    COUNT(*) as count
FROM attendance
WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY attendance_date, status
ORDER BY attendance_date DESC
```

**Panel Settings:**
- Type: **Time Series**
- Title: "Attendance Trend (Last 30 Days)"

### Panel 3: Staff Present Today

**Query:**
```sql
SELECT 
    s.staff_name,
    a.status,
    a.check_in_time,
    a.check_out_time
FROM attendance a
JOIN staff s ON a.staff_id = s.staff_id
WHERE a.attendance_date = CURDATE()
AND a.status = 'present'
ORDER BY s.staff_name
```

**Panel Settings:**
- Type: **Table**
- Title: "Staff Present Today"

## Step 5: Configure Alerts (Optional)

Add alert conditions to notify you of:
- High number of pending leave requests
- Low leave balance
- Attendance anomalies

1. On any panel, click **Alert**
2. Set condition and notification channel

## Database Schema Reference

### Key Tables for Queries

**staff**
- `staff_id`, `staff_name`, `staff_email`, `department_id`, `team_id`

**attendance**
- `attendance_id`, `staff_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`

**leave_requests**
- `leave_request_id`, `staff_id`, `leave_type`, `from_date`, `to_date`, `status`, `created_at`

## Troubleshooting

### "Database Connection Failed"
- Check MySQL is running: `docker ps`
- Verify credentials: root/root
- Test connection: `mysql -h 127.0.0.1 -P 3307 -u root -p`

### "No data in dashboard"
- Check data exists in MySQL: Use phpMyAdmin at `http://localhost:8081`
- Verify SQL query in Grafana query editor
- Check date ranges in queries

### Slow queries
- Add indexes to frequently queried columns:
  ```sql
  CREATE INDEX idx_attendance_date ON attendance(attendance_date);
  CREATE INDEX idx_leave_status ON leave_requests(status);
  CREATE INDEX idx_leave_year ON leave_requests(leave_type, YEAR(from_date));
  ```

## Performance Tips

1. **Limit time ranges** in queries (use `DATE_SUB` for recent data)
2. **Use appropriate panel types** (Table for details, Stat for summaries)
3. **Enable caching** in Grafana settings
4. **Schedule dashboard refreshes** instead of real-time updates

## Useful MySQL Queries

### Staff Leave Balance Report
```sql
SELECT 
    s.staff_id,
    s.staff_name,
    d.department_name,
    20 as annual_leave,
    COALESCE(SUM(DATEDIFF(lr.to_date, lr.from_date) + 1), 0) as used,
    20 - COALESCE(SUM(DATEDIFF(lr.to_date, lr.from_date) + 1), 0) as remaining
FROM staff s
LEFT JOIN departments d ON s.department_id = d.department_id
LEFT JOIN leave_requests lr ON s.staff_id = lr.staff_id 
    AND lr.status = 'approved'
    AND YEAR(lr.from_date) = YEAR(NOW())
GROUP BY s.staff_id
ORDER BY remaining ASC;
```

### Attendance Statistics by Department
```sql
SELECT 
    d.department_name,
    CURDATE() as date,
    COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present,
    COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent,
    COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late,
    COUNT(CASE WHEN a.status = 'on leave' THEN 1 END) as on_leave
FROM attendance a
JOIN staff s ON a.staff_id = s.staff_id
JOIN departments d ON s.department_id = d.department_id
WHERE a.attendance_date = CURDATE()
GROUP BY d.department_name;
```

## Export and Share Dashboards

1. Click the **Share** button on any dashboard
2. Choose **Export** or **Generate link**
3. Share the JSON or link with team members

