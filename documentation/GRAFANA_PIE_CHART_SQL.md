# Grafana SQL Query for Present Days Pie Chart

## Simple Query - Count Present Status Only

```sql
SELECT 
  'Present' as status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $staff_id
AND status = 'present';
```

---

## Enhanced Query - Present vs Other Days

This query shows Present vs All Other Days combined:

```sql
SELECT 
  CASE 
    WHEN status = 'present' THEN 'Present'
    ELSE 'Other Days'
  END as status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $staff_id
GROUP BY CASE WHEN status = 'present' THEN 'Present' ELSE 'Other Days' END;
```

---

## Breakdown Query - Present Only (For Simple Pie Chart)

If you just want to display how many days present vs days recorded:

```sql
SELECT 
  COUNT(CASE WHEN status = 'present' THEN 1 END) as 'Present',
  COUNT(CASE WHEN status != 'present' THEN 1 END) as 'Not Present'
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $staff_id;
```

---

## Setup Instructions for Grafana

1. **Open Grafana**: `http://localhost:3000`

2. **Create/Edit Dashboard**:
   - Click the **"+" icon** → Select **"Create Dashboard"**
   - Or edit your existing dashboard

3. **Add Panel**:
   - Click **"Add a new panel"**
   - Select **"Pie Chart"** visualization

4. **Configure Query**:
   - **Query Type**: Select **"MySQL"** datasource
   - **Copy and paste one of the SQL queries above**
   - Replace `$staff_id` with the actual staff ID or keep as variable

5. **Configure Pie Chart Display**:
   - **Pie Chart Type**: Select **"Pie"**
   - **Display**: Show percentages
   - **Legend**: Check "Show legend"
   - **Colors**: Set green for "Present", red/gray for "Other Days"

6. **Variable Setup** (Optional but Recommended):
   - Go to **Dashboard Settings** (gear icon)
   - Click **Variables**
   - Create new variable:
     - **Name**: `staff_id`
     - **Type**: `Query` or `Constant`
     - **Query**: `SELECT DISTINCT staff_id FROM attendance ORDER BY staff_id`
   - This allows dropdown to select different staff members

7. **Save & Test**:
   - Click **"Save"** button
   - Test by visiting: `http://localhost:3000/d/YOUR_DASHBOARD_UID/dashboard-name?var-staff_id=1`

---

## Important Notes

- **The `$staff_id` variable** is automatically passed from Laravel
- **Current Month**: The query uses `YEAR(NOW())` and `MONTH(NOW())` to get current month
- **Data Update**: Changes in attendance will show in Grafana within 30 seconds (auto-refresh)
- **Staff ID**: Make sure the staff_id being passed matches the attendance table data

---

## Testing Your Query

Before using in Grafana, test the query directly in **phpMyAdmin**:

1. Open: `http://localhost:8081`
2. Login: `root` / `root`
3. Select database: `staffAttend_data`
4. Go to **SQL tab**
5. Paste the query (replace `$staff_id` with an actual number like `1`)
6. Click **Execute**

Example:
```sql
SELECT 
  'Present' as status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = 1
AND status = 'present';
```

---

## Recommended Query for Your Pie Chart

Based on your requirement (only showing "Present" and Leave Status):

```sql
SELECT 
  CASE 
    WHEN status = 'present' THEN 'Present'
    WHEN status = 'on leave' THEN 'On Leave'
    ELSE 'Other'
  END as status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $staff_id
GROUP BY CASE 
  WHEN status = 'present' THEN 'Present'
  WHEN status = 'on leave' THEN 'On Leave'
  ELSE 'Other'
END;
```

This will show:
- **Present** (Green) - Days marked as present
- **On Leave** (Blue) - Days marked as on leave  
- **Other** (Gray) - All other statuses combined

---

## Troubleshooting

**Query returns no data?**
- Check that staff_id variable is being passed correctly
- Verify attendance records exist for current month: `SELECT * FROM attendance WHERE YEAR(attendance_date) = 2025 AND MONTH(attendance_date) = 12 LIMIT 5;`
- Check MySQL datasource connection in Grafana

**Variable not working?**
- Make sure variable name in query matches: `$staff_id` (with the dollar sign)
- Test by manually entering a staff ID number instead of the variable

**Colors not showing?**
- Go to Pie Chart **"Display"** tab
- Under **"Series colors"**, assign colors manually:
  - Present → Green (#22c55e)
  - On Leave → Blue (#3b82f6)
  - Other → Gray (#6b7280)
