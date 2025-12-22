# Grafana Dashboard Setup - Complete Steps

## Prerequisites ✓
- MySQL running on port 3307 ✓
- Grafana running on port 3000 ✓
- Staff attendance data in database ✓

---

## STEP 1: Add MySQL Data Source to Grafana

1. **Login to Grafana**
   - Go to http://localhost:3000
   - Username: `admin`
   - Password: `admin`

2. **Add Data Source**
   - Click **Configuration** (gear icon) → **Data Sources**
   - Click **Add data source**
   - Select **MySQL**
   - Fill in the following:
     - **Name**: `Staff Attendance DB`
     - **Host**: `mysql:3306`
     - **Database**: `staff_attendance`
     - **User**: `root`
     - **Password**: `root`
   - Click **Save & Test**
   - You should see "Database Connection OK"

---

## STEP 2: Create Admin Dashboard

### Create Dashboard
1. Click **Dashboards** → **Create** → **New Dashboard**
2. Click **Add a new panel**

### Panel 1: Total Staff
- **Query**:
```sql
SELECT COUNT(*) as value FROM staff;
```
- **Settings**:
  - Title: `Total Staff`
  - Visualization: **Stat**
  - Unit: `short`
- Click **Apply**

### Panel 2: Present Today
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() AND status = 'present';
```
- **Settings**:
  - Title: `Present Today`
  - Visualization: **Stat**
  - Color: `green`
- Click **Apply**

### Panel 3: On Leave Today
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() AND status = 'leave';
```
- **Settings**:
  - Title: `On Leave`
  - Visualization: **Stat**
  - Color: `blue`
- Click **Apply**

### Panel 4: Absent Today
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE DATE(attendance_date) = CURDATE() AND status = 'absent';
```
- **Settings**:
  - Title: `Absent Today`
  - Visualization: **Stat**
  - Color: `red`
- Click **Apply**

### Save Dashboard
- Click **Save** (top right)
- Name: `Attendance - Admin`
- Click **Save**

---

## STEP 3: Create Staff Dashboard (with Variable)

### Create Dashboard
1. Click **Dashboards** → **Create** → **New Dashboard**

### Create Variable First
1. Click **Dashboard Settings** (gear icon, top right)
2. Select **Variables**
3. Click **New variable**
4. Fill in:
   - **Name**: `staff_id`
   - **Label**: `Staff Member`
   - **Type**: `Query`
   - **Data source**: `Staff Attendance DB`
   - **Query**: 
   ```sql
   SELECT staff_id as __value, CONCAT(staff_id, ' - ', staff_name) as __text FROM staff ORDER BY staff_name
   ```
5. Click **Update**
6. Click **Save dashboard** (at bottom of popup)

### Add Panels
Click **Add a new panel** for each:

### Panel 1: Total Present
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE staff_id = $staff_id AND status = 'present';
```
- **Settings**:
  - Title: `Total Present`
  - Visualization: **Stat**
  - Color: `green`
- Click **Apply**

### Panel 2: Total Absent
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE staff_id = $staff_id AND status = 'absent';
```
- **Settings**:
  - Title: `Total Absent`
  - Visualization: **Stat**
  - Color: `red`
- Click **Apply**

### Panel 3: Total Late
- **Query**:
```sql
SELECT COUNT(*) as value FROM attendance 
WHERE staff_id = $staff_id AND status = 'late';
```
- **Settings**:
  - Title: `Total Late`
  - Visualization: **Stat**
  - Color: `orange`
- Click **Apply**

### Panel 4: Attendance Breakdown (Pie Chart)
- **Query**:
```sql
SELECT 
  CONCAT(UPPER(LEFT(status, 1)), LOWER(SUBSTRING(status, 2))) as Status,
  COUNT(*) as value
FROM attendance
WHERE staff_id = $staff_id
GROUP BY status;
```
- **Settings**:
  - Title: `Attendance Breakdown`
  - Visualization: **Pie Chart**
- Click **Apply**

### Panel 5: Last 30 Days (Graph)
- **Query**:
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
- **Settings**:
  - Title: `Last 30 Days`
  - Visualization: **Time series**
- Click **Apply**

### Save Dashboard
- Click **Save** (top right)
- Name: `Attendance - Staff`
- Click **Save**

---

## STEP 4: Test the Dashboards

### Admin Dashboard
1. Go to **Dashboards** → **Attendance - Admin**
2. All panels should show data
3. Numbers should be live and update in real-time

### Staff Dashboard
1. Go to **Dashboards** → **Attendance - Staff**
2. You'll see a dropdown "Staff Member" at the top
3. Select a staff member
4. All panels will update to show that staff's data

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Data Source Connection Failed | Check MySQL is running: `docker ps` should show mysql_staff container |
| "Unknown column" error | Verify column names: `staff_id`, `attendance_date`, `status` exist in database |
| Variable not showing | Make sure you created the variable BEFORE adding panels that use `$staff_id` |
| No data in panels | Insert test attendance data: Run setup_data.php in Laravel |
| Panels show 0 | Check if attendance records exist for today: Run query in phpMyAdmin (port 8081) |

---

## Quick Check: Verify Database

1. Go to http://localhost:8081 (phpMyAdmin)
2. Login: `root` / `root`
3. Select **staff_attendance** database
4. Check **staff** table has records
5. Check **attendance** table has records for today

---

## Notes

- Admin Dashboard: Shows **real-time** totals for all staff
- Staff Dashboard: Shows **personal** stats for selected staff member
- Variables use `$variable_name` syntax (NOT `${variable_name}`)
- Dashboards refresh automatically every 30 seconds by default
- You can customize refresh rate in Dashboard Settings

---

## Next Steps

After both dashboards are working:

1. **Optional**: Share dashboards with other users
2. **Optional**: Set auto-refresh rate (Dashboard Settings → Refresh)
3. **Optional**: Add alerts (Panel → Alert tab)
4. **Optional**: Export dashboards as JSON for backup

