
# GRAFANA SETUP GUIDE - ATTENDANCE PIE CHART

## Overview
This guide will walk you through setting up a Grafana pie chart connected to your MySQL database to display monthly attendance statistics.

---

## STEP 1: Access Grafana Dashboard

1. Open your browser and go to: `http://localhost:3000`
2. Login with:
   - **Username**: `admin`
   - **Password**: `admin`
   - (Change password if prompted)

---

## STEP 2: Add MySQL Data Source

### 2.1 Navigate to Data Sources
- Click the **gear icon** (⚙️) in the left sidebar → **Data Sources**
- Or go to: Settings → Data Sources

### 2.2 Create New Data Source
1. Click **+ Add data source** (blue button, top right)
2. Select **MySQL** from the list
3. Fill in the configuration:

```
Name:               MySQL Attendance
Description:        MySQL database for staff attendance
Host:               mysql:3306          (Docker container name and port)
Database:           staffAttend_data
User:               root
Password:           root
MySQL version:      8.0
SSL Mode:           skip-verify
```

4. Click **Save & Test**
   - You should see: ✅ "Database Connection OK"

---

## STEP 3: Create a New Dashboard

### 3.1 Create Dashboard
1. Click the **+** icon in the left sidebar → **Dashboard**
2. Click **Add new panel**

### 3.2 Configure the Panel

1. **Panel Title**: "Monthly Attendance Status"

2. **Choose Visualization Type**:
   - Click the **Pie Chart** icon in the visualization selector (right side)

3. **Select Data Source**:
   - From dropdown, select "MySQL Attendance"

---

## STEP 4: Add SQL Query for Pie Chart

### 4.1 Enter the Query

In the **Query** section, paste this SQL:

```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        WHEN status = 'leave' THEN 'On Leave'
        WHEN status = 'el' THEN 'Emergency Leave'
        WHEN status = 'on leave' THEN 'On Leave'
        WHEN status = 'half day' THEN 'Half Day'
        ELSE status
    END AS `Status`,
    COUNT(*) AS `Count`
FROM attendance
WHERE staff_id = $__myVar  
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
ORDER BY `Count` DESC
```

### 4.2 Configure Data Format
- In the Query section, set **Format as**: Table
- Grafana will automatically detect columns: Status (string) and Count (number)

---

## STEP 5: Configure Pie Chart Display

### 5.1 Pie Chart Options
1. Go to the **Pie Chart** settings (right panel)
2. Configure:

```
Display Mode:           Pie
Pie Type:               Donut (optional)
Legend:                 Table (show all statuses)
Tooltip:                On hover
Decimal places:         0
```

### 5.2 Field Configuration
1. Click **Status** field → Set as **Series**
2. Click **Count** field → Set as **Values**

### 5.3 Color Setup
1. Go to **Color scheme** section
2. Set colors for each status:
   - Present: Green (#22c55e)
   - Absent: Red (#ef4444)
   - Late: Yellow (#eab308)
   - On Leave: Blue (#3b82f6)
   - EL: Orange (#f97316)
   - Half Day: Purple (#a855f7)

---

## STEP 6: Add Variables for Dynamic Dashboard

### 6.1 Add Staff ID Variable
1. Go to **Dashboard settings** (gear icon)
2. Click **Variables** → **+ Add variable**
3. Configure:

```
Name:               staffId
Label:              Staff Member
Type:               Query
Data Source:        MySQL Attendance
Query:              SELECT DISTINCT staff_id FROM staff
Show label:         On
Include all option: On
```

4. Click **Update**

### 6.2 Modify Your Query
Replace `$staffId` in your SQL with:
```
SELECT CASE ... WHERE staff_id = ${staffId}
```

---

## STEP 7: Create Month Selector (Optional)

### 7.1 Add Date Range Variable
1. Dashboard Settings → Variables → **+ Add variable**
2. Configure:

```
Name:               dateRange
Label:              Date Range
Type:               Interval
Values:             1m, 3m, 6m, 1y
Current:            1m
```

3. Update your query to use:
```
AND attendance_date >= DATE_SUB(NOW(), INTERVAL ${dateRange})
```

---

## STEP 8: Save and Share Dashboard

### 8.1 Save Dashboard
1. Click **Save** (top right)
2. Enter dashboard name: "Staff Attendance Dashboard"
3. Choose folder and click **Save**

### 8.2 Share Dashboard
1. Click **Share** (top right)
2. Copy the dashboard link
3. Share with team members

---

## STEP 9: Connect to Laravel Dashboard

### 9.1 Get Dashboard Panel ID
1. In your dashboard, click on the pie chart panel
2. Copy the **Panel ID** from the URL or panel header

### 9.2 Create Embedded Panel URL
The iframe URL format is:
```
http://localhost:3000/d-solo/[DASHBOARD_UID]/[DASHBOARD_NAME]?orgId=1&panelId=[PANEL_ID]&var-staffId=${staffId}&kiosk=tv&refresh=30s
```

Example:
```
http://localhost:3000/d-solo/abc123def456/attendance-dashboard?orgId=1&panelId=1&var-staffId=1&kiosk=tv&refresh=30s
```

---

## SQL QUERY REFERENCE

### Query 1: Basic Monthly Breakdown
```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    COUNT(*) AS Count
FROM attendance
WHERE staff_id = ${staffId}
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
```

### Query 2: With Percentages
```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    COUNT(*) AS Count,
    ROUND((COUNT(*) / (SELECT COUNT(*) FROM attendance 
        WHERE staff_id = ${staffId} 
        AND YEAR(attendance_date) = YEAR(NOW())
        AND MONTH(attendance_date) = MONTH(NOW())) * 100), 1) AS Percentage
FROM attendance
WHERE staff_id = ${staffId}
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
```

### Query 3: Yearly Summary
```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    COUNT(*) AS Count
FROM attendance
WHERE staff_id = ${staffId}
AND YEAR(attendance_date) = YEAR(NOW())
GROUP BY status
```

---

## TROUBLESHOOTING

### Issue: "Database Connection Failed"
- ✅ Check if MySQL container is running: `docker ps`
- ✅ Verify credentials (root/root)
- ✅ Check host is `mysql:3306` (not localhost)

### Issue: "No data returned"
- ✅ Check if attendance records exist in database
- ✅ Verify staff_id exists in the staff table
- ✅ Check query syntax and date formats

### Issue: "Pie chart not displaying"
- ✅ Make sure you have at least 2 rows of data
- ✅ Verify Status and Count columns are properly mapped
- ✅ Check if data is showing in table format first

### Issue: "Variable not working"
- ✅ Refresh page after adding variables
- ✅ Ensure query uses correct variable syntax: `${variableName}`
- ✅ Check variable datasource connection

---

## NEXT STEPS

1. ✅ Test the pie chart with sample data
2. ✅ Customize colors to match your branding
3. ✅ Add more panels (table, stats, trends)
4. ✅ Create alerts for low attendance
5. ✅ Export dashboard as JSON for backup

---

## ADDITIONAL FEATURES

### Add Table Panel
Create a second panel showing detailed records:
```sql
SELECT 
    attendance_date,
    status,
    check_in_time,
    check_out_time,
    remarks
FROM attendance
WHERE staff_id = ${staffId}
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
ORDER BY attendance_date DESC
```

### Add Stat Panels (Present, Absent, Late)
Create separate panels for each metric:
```sql
SELECT COUNT(*) as value
FROM attendance
WHERE staff_id = ${staffId}
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND status = 'present'
```

### Add Trend Chart
Show monthly comparison:
```sql
SELECT 
    DATE_FORMAT(attendance_date, '%Y-%m') as Month,
    COUNT(*) as Count
FROM attendance
WHERE staff_id = ${staffId}
GROUP BY MONTH(attendance_date)
ORDER BY Month DESC
LIMIT 12
```

