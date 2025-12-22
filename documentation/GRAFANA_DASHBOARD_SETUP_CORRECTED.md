# Grafana Dashboard Setup Guide - CORRECTED

## Database Columns Reference
```
staff table: staff_id, staff_name, staff_email, department_id, team_id, created_at
attendance table: id, staff_id, attendance_date, check_in_time, check_out_time, status (present/absent/late/leave), remarks, created_at, updated_at
```

---

## Admin Dashboard (Total Staff, Present Today, On Leave)

### Query 1: Total Staff Count
```sql
SELECT 
  COUNT(*) as value,
  'Total Staff' as metric
FROM staff;
```

### Query 2: Present Today
```sql
SELECT 
  COUNT(*) as value,
  'Present Today' as metric
FROM attendance
WHERE DATE(attendance_date) = CURDATE()
  AND status = 'present';
```

### Query 3: On Leave Today
```sql
SELECT 
  COUNT(*) as value,
  'On Leave' as metric
FROM attendance
WHERE DATE(attendance_date) = CURDATE()
  AND status = 'leave';
```

### Query 4: Absent Today
```sql
SELECT 
  COUNT(*) as value,
  'Absent Today' as metric
FROM attendance
WHERE DATE(attendance_date) = CURDATE()
  AND status = 'absent';
```

### Query 5: Department-wise Attendance (Table)
```sql
SELECT 
  d.department_name as Department,
  COUNT(DISTINCT s.staff_id) as 'Total Staff',
  SUM(CASE WHEN a.status = 'present' AND DATE(a.attendance_date) = CURDATE() THEN 1 ELSE 0 END) as 'Present Today',
  SUM(CASE WHEN a.status = 'absent' AND DATE(a.attendance_date) = CURDATE() THEN 1 ELSE 0 END) as 'Absent Today',
  SUM(CASE WHEN a.status = 'leave' AND DATE(a.attendance_date) = CURDATE() THEN 1 ELSE 0 END) as 'On Leave'
FROM staff s
LEFT JOIN departments d ON s.department_id = d.department_id
LEFT JOIN attendance a ON s.staff_id = a.staff_id AND DATE(a.attendance_date) = CURDATE()
GROUP BY d.department_id, d.department_name
ORDER BY Department;
```

---

## Staff Dashboard (Total Present, Total Absent, Total Late)

### Important: Add Variable to Grafana First

**Go to Dashboard Settings → Variables → Create New Variable**

- **Name**: `staff_id`
- **Label**: Staff Member
- **Type**: Query
- **Data source**: MySQL (Staff Attendance DB)
- **Query**: 
```sql
SELECT staff_id as __value, staff_name as __text FROM staff ORDER BY staff_name
```

---

### Query 1: Staff Total Present
```sql
SELECT 
  COUNT(*) as value,
  'Total Present' as metric
FROM attendance
WHERE staff_id = $staff_id
  AND status = 'present';
```

### Query 2: Staff Total Absent
```sql
SELECT 
  COUNT(*) as value,
  'Total Absent' as metric
FROM attendance
WHERE staff_id = $staff_id
  AND status = 'absent';
```

### Query 3: Staff Total Late
```sql
SELECT 
  COUNT(*) as value,
  'Total Late' as metric
FROM attendance
WHERE staff_id = $staff_id
  AND status = 'late';
```

### Query 4: Staff Attendance Summary (Pie Chart)
```sql
SELECT 
  CONCAT(UPPER(LEFT(status, 1)), LOWER(SUBSTRING(status, 2))) as Status,
  COUNT(*) as value
FROM attendance
WHERE staff_id = $staff_id
GROUP BY status;
```

### Query 5: Staff Last 30 Days Attendance (Graph)
```sql
SELECT 
  DATE(attendance_date) as Time,
  status as metric,
  COUNT(*) as value
FROM attendance
WHERE staff_id = $staff_id
  AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(attendance_date), status
ORDER BY attendance_date;
```

### Query 6: Staff Attendance Monthly Trend
```sql
SELECT 
  DATE_FORMAT(attendance_date, '%Y-%m') as Month,
  SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as Present,
  SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as Absent,
  SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as Late
FROM attendance
WHERE staff_id = $staff_id
GROUP BY DATE_FORMAT(attendance_date, '%Y-%m')
ORDER BY Month DESC
LIMIT 12;
```

---

## Step-by-Step Setup Instructions

### Step 1: Connect MySQL to Grafana

1. Login to Grafana (http://localhost:3000)
   - Default: admin / admin
2. Go to **Configuration** → **Data Sources** → **Add data source**
3. Select **MySQL**
4. Configure:
   - **Name**: `Staff Attendance DB`
   - **Host**: `mysql:3306` (Docker) or `localhost:3307` (local)
   - **Database**: `staff_attendance`
   - **User**: `root`
   - **Password**: `root`
5. Click **Save & Test**

### Step 2: Create Admin Dashboard

1. **Dashboards** → **Create** → **New Dashboard**
2. Add panels one by one:

#### Panel 1: Total Staff (Stat visualization)
- Query: Query 1 from Admin section above
- Title: "Total Staff"
- Visualization: Stat
- Orientation: Auto

#### Panel 2: Present Today (Stat visualization)
- Query: Query 2
- Title: "Present Today"
- Visualization: Stat
- Color: Green

#### Panel 3: On Leave Today (Stat visualization)
- Query: Query 3
- Title: "On Leave"
- Visualization: Stat
- Color: Blue

#### Panel 4: Absent Today (Stat visualization)
- Query: Query 4
- Title: "Absent Today"
- Visualization: Stat
- Color: Red

#### Panel 5: Department Breakdown (Table)
- Query: Query 5
- Title: "Department Attendance"
- Visualization: Table

3. Save: **Dashboard Settings** → **Save dashboard** as `"Attendance - Admin"`

### Step 3: Create Staff Dashboard

1. **Dashboards** → **Create** → **New Dashboard**
2. **Dashboard Settings** → **Variables** → **Add Variable**
   - Name: `staff_id`
   - Label: `Staff Member`
   - Type: `Query`
   - Data source: `Staff Attendance DB`
   - Query: `SELECT staff_id as __value, staff_name as __text FROM staff ORDER BY staff_name`

3. Add panels:

#### Panel 1: Total Present (Stat)
- Query: Query 1 from Staff section
- Title: "Total Present"
- Color: Green

#### Panel 2: Total Absent (Stat)
- Query: Query 2
- Title: "Total Absent"
- Color: Red

#### Panel 3: Total Late (Stat)
- Query: Query 3
- Title: "Total Late"
- Color: Yellow

#### Panel 4: Attendance Summary (Pie Chart)
- Query: Query 4
- Title: "Attendance Breakdown"
- Visualization: Pie Chart

#### Panel 5: Last 30 Days (Time Series)
- Query: Query 5
- Title: "Last 30 Days Attendance"
- Visualization: Time Series

#### Panel 6: Monthly Trend (Bar Chart)
- Query: Query 6
- Title: "Monthly Trends"
- Visualization: Bar Chart

4. Save: `"Attendance - Staff"`

---

## Troubleshooting

| Error | Solution |
|-------|----------|
| "Unknown column 'staff_id'" | Ensure variable syntax is `$staff_id` not `${staff_id}` in SQL |
| Variable not showing dropdown | Query must return __value and __text fields |
| No data in charts | Check if attendance data exists for selected staff/date |
| Connection refused | Ensure MySQL service is running on port 3306 |
| "Access denied" | Check MySQL credentials match docker-compose.yml |

### Quick Test Query

Run this in Grafana to verify connection:

```sql
SELECT COUNT(*) as total_attendance_records FROM attendance;
```

Should return a number > 0 if data exists.

---

## Docker Reference

From your docker-compose.yml:

```yaml
mysql:
  port: 3307  # External port
  internal: 3306  # Used by Grafana in Docker
  database: staff_attendance
  user: root
  password: root

grafana:
  port: 3000
  default user: admin
  default pass: admin
```

**Key**: Use `mysql:3306` in Grafana (Docker internal DNS), not `localhost:3307`

---

## Variable Syntax Cheat Sheet

In Grafana MySQL queries, variables use one of these formats:

```sql
-- Number variable (no quotes)
WHERE staff_id = $staff_id

-- String variable (with quotes)
WHERE department_name = '$department_name'

-- Multiple values
WHERE staff_id IN ($staff_id)
```

**DO NOT use `${variable_name}`** in simple MySQL datasources. Use `$variable_name` instead.

