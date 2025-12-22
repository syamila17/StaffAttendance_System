# COPY-PASTE SQL COMMANDS FOR GRAFANA

## For Pie Chart - Monthly Breakdown (RECOMMENDED)

```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        WHEN status = 'leave' THEN 'On Leave'
        WHEN status = 'el' THEN 'Emergency Leave'
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

## For Pie Chart with Percentages

```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        WHEN status = 'leave' THEN 'On Leave'
        ELSE status
    END AS Status,
    COUNT(*) AS Count,
    ROUND((COUNT(*) / (SELECT COUNT(*) FROM attendance 
        WHERE staff_id = $__myVar 
        AND YEAR(attendance_date) = YEAR(NOW())
        AND MONTH(attendance_date) = MONTH(NOW())) * 100), 1) AS Percentage
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
ORDER BY Count DESC
```

---

## For Table Panel (Detailed Records)

```sql
SELECT 
    attendance_date AS Date,
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    TIME_FORMAT(check_in_time, '%H:%i') AS CheckIn,
    TIME_FORMAT(check_out_time, '%H:%i') AS CheckOut,
    remarks AS Remarks
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
ORDER BY attendance_date DESC
```

---

## For Stats Boxes (One Query)

```sql
SELECT 
    'Present' AS metric, COUNT(*) AS value
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND status = 'present'

UNION ALL

SELECT 
    'Absent' AS metric, COUNT(*) AS value
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND status = 'absent'

UNION ALL

SELECT 
    'Late' AS metric, COUNT(*) AS value
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND status = 'late'

UNION ALL

SELECT 
    'On Leave' AS metric, COUNT(*) AS value
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND (status = 'leave' OR status = 'on leave' OR status = 'el')
```

---

## For Trend Chart (Last 12 Months)

```sql
SELECT 
    DATE_FORMAT(attendance_date, '%b %Y') AS Month,
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    COUNT(*) AS Count
FROM attendance
WHERE staff_id = $__myVar
AND attendance_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(attendance_date, '%Y-%m'), status
ORDER BY DATE_FORMAT(attendance_date, '%Y-%m') DESC, Status
```

---

## For YTD (Year to Date) Summary

```sql
SELECT 
    CASE 
        WHEN status = 'present' THEN 'Present'
        WHEN status = 'absent' THEN 'Absent'
        WHEN status = 'late' THEN 'Late'
        ELSE status
    END AS Status,
    COUNT(*) AS TotalDays,
    ROUND((COUNT(*) / (SELECT COUNT(*) FROM attendance 
        WHERE staff_id = $__myVar 
        AND YEAR(attendance_date) = YEAR(NOW())) * 100), 1) AS YTDPercentage
FROM attendance
WHERE staff_id = $__myVar
AND YEAR(attendance_date) = YEAR(NOW())
GROUP BY status
ORDER BY TotalDays DESC
```

---

## Test Query (Without Variables - Use phpMyAdmin)

```sql
-- Replace 1 with actual staff_id
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
WHERE staff_id = 1
AND YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status
ORDER BY Count DESC
```

---

## Check Data Exists Query

```sql
-- Check if attendance records exist for current month
SELECT 
    COUNT(*) as TotalRecords,
    COUNT(DISTINCT staff_id) as UniqueStaff,
    COUNT(DISTINCT DATE(attendance_date)) as Uniquedays
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW());

-- Show what statuses are recorded
SELECT 
    status, 
    COUNT(*) as Count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
GROUP BY status;

-- Show recent records
SELECT 
    staff_id,
    attendance_date,
    status,
    check_in_time,
    check_out_time
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
ORDER BY attendance_date DESC
LIMIT 10;
```

---

## Variable Configuration (In Grafana)

### Staff ID Variable
```
Type: Query
Data Source: MySQL Attendance
Query: SELECT DISTINCT staff_id FROM staff ORDER BY staff_id
Regex: 
Selection Options: Multi-value, Include All Option
```

### Month Variable
```
Type: Interval
Options: 1m, 3m, 6m, 1y
Current: 1m
Auto option: off
```

### Date Range Variable
```
Type: Interval
Options: 7d, 14d, 30d, 90d
Current: 30d
```

---

## Template Variables Reference

| Variable | Use Case |
|----------|----------|
| `$__myVar` | Single value variable (staff_id) |
| `${staffId}` | Named variable (staffId) |
| `$__timeFrom` | Time range start (timestamp) |
| `$__timeTo` | Time range end (timestamp) |
| `NOW()` | Current date/time |
| `YEAR(NOW())` | Current year |
| `MONTH(NOW())` | Current month (1-12) |

---

## Testing in phpMyAdmin

1. Go to: http://localhost:8081
2. Login: root / root
3. Select: staffAttend_data
4. Go to: SQL tab
5. Paste query (replace $__myVar with actual staff_id like 1)
6. Click Execute
7. Should see results with Status and Count columns

---

## Tips

✓ Always test queries in phpMyAdmin first  
✓ Make sure attendance table has data for current month  
✓ Use LIMIT 10 when debugging to see partial results  
✓ Check date formats match your data  
✓ Remember Grafana uses timestamp in milliseconds for time ranges  

