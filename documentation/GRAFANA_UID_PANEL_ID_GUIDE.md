# How to Find Grafana Dashboard UID and Panel ID

## ✅ Database Connection FIXED!

Great news! Your database connection is now working. The error "Variable 'max_connections' is a GLOBAL variable" has been eliminated.

**What was fixed:**
- ✅ Removed `MYSQL_ATTR_INIT_COMMAND` from `config/database.php`
- ✅ Removed `AppServiceProvider` connection test that was logging errors
- ✅ Cleared Laravel cache
- ✅ Verified MySQL connection is working (8.0.44, 28 tables)

---

## Step 1: Find Your Grafana Dashboard UID

### Method 1: From Grafana URL (Easiest)

1. **Open Grafana** in your browser:
   ```
   http://localhost:3000
   ```

2. **Navigate to your attendance/pie chart dashboard**

3. **Look at the browser address bar** - you'll see a URL like:
   ```
   http://localhost:3000/d/abc123def456/monthly-attendance-pie-chart
   ```

4. **The UID is the alphanumeric string after `/d/`**
   - In this example: `abc123def456`
   - This is your `GRAFANA_DASHBOARD_UID`

### Method 2: From Dashboard Settings

1. Open your dashboard in Grafana
2. Click the **gear icon** (⚙️) in the top-right corner
3. Select **Dashboard settings**
4. Look at the URL - the UID will be visible after `/d/`
5. You can also copy it directly from there

### Method 3: From Dashboard Menu

1. In Grafana, click the **dashboard icon** (looks like a grid)
2. Find your dashboard in the list
3. Hover over it to see details including the UID
4. Or click it and check the URL

---

## Step 2: Find Your Panel ID (For Pie Chart)

### Method 1: Hover Over the Panel

1. **Open your dashboard** with the pie chart
2. **Move your mouse over the pie chart panel**
3. You'll see a **toolbar appear at the top-right** of the panel
4. Look for the **panel number/ID** (usually shown in the toolbar)
5. Click the **dropdown arrow** (⌄) next to the panel title
6. Select **"Panel JSON"** or look at the panel header - the ID is displayed

### Method 2: Inspect Panel JSON

1. **Open your dashboard** in Grafana
2. **Click on the pie chart panel** to select it
3. In the **panel header**, look for the panel number (usually `1`, `2`, etc.)
4. Or click the **dropdown menu** (⌄) on the panel and select **"View JSON"**
5. The `id` field will show your Panel ID

### Method 3: Check Dashboard JSON

1. **Open your dashboard**
2. Click the **gear icon** (⚙️) → **Dashboard settings**
3. Look for the **JSON model** or **Edit JSON**
4. Search for `"panels"` - each panel has an `"id"` field
5. Find your pie chart panel and note its ID

---

## Step 3: Update Your `.env` File

Open this file: `staff_attendance/.env`

Find these lines and update them with your actual values:

```env
GRAFANA_URL=http://localhost:3000
GRAFANA_DASHBOARD_UID=abc123def456
GRAFANA_PIE_CHART_PANEL_ID=1
```

**Example with real values:**
```env
GRAFANA_URL=http://localhost:3000
GRAFANA_DASHBOARD_UID=adtx5zp123
GRAFANA_PIE_CHART_PANEL_ID=2
```

---

## Step 4: Verify Your Panel Has the Correct SQL Query

Open your Grafana dashboard and click on your pie chart panel:

1. **Click on the pie chart**
2. Click the **"Edit"** button (pencil icon)
3. Look at the **SQL query** in the query editor
4. It should be something like:

```sql
SELECT 
  status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
  AND MONTH(attendance_date) = MONTH(NOW())
  AND staff_id = $staff_id
GROUP BY status
ORDER BY count DESC
```

**Important:** Make sure:
- The table name is correct: `attendance` (not `attendances`)
- The column names match your database
- The `staff_id` variable is set as `$staff_id`

---

## Step 5: Verify Your MySQL Datasource

1. **Open Grafana**: http://localhost:3000
2. Click the **gear icon** (⚙️) → **Data sources**
3. You should see a MySQL datasource
4. Click on it to verify:
   - **Host**: `mysql:3306` (if using Docker network) or `127.0.0.1:3307` (if local)
   - **Database**: `staffAttend_data`
   - **Username**: `root`
   - **Password**: `root` (or your password)
5. Click **"Test"** button to verify connection
6. Should show: **"Database Connection OK"**

---

## Step 6: Clear Laravel Cache and Test

Run these commands in your terminal:

```powershell
cd 'C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance'
php artisan config:clear
php artisan cache:clear
```

Then open your dashboard in Laravel:
```
http://localhost:8000/login
```

Login and navigate to your staff dashboard. The pie chart should now display.

---

## Troubleshooting

### Issue: "No data" in pie chart

**Check these things:**

1. **Do you have attendance records for this month?**
   - Open phpMyAdmin: http://localhost:8081
   - Login with: root / root
   - Go to `staffAttend_data` → `attendance` table
   - Run this query:
     ```sql
     SELECT status, COUNT(*) as count
     FROM attendance
     WHERE YEAR(attendance_date) = 2025
     AND MONTH(attendance_date) = 12
     GROUP BY status;
     ```
   - If it returns 0 rows, you need to add test attendance data

2. **Is the staff_id variable being passed?**
   - In Grafana, open your dashboard
   - Look at the URL - it should have: `?var-staff_id=1` (or some number)
   - If not, you may need to set a default staff_id in Grafana dashboard variables

3. **Are the column names correct?**
   - The query uses `status` and `attendance_date` columns
   - Your table might use different names
   - Check your table structure in phpMyAdmin

### Issue: Panel ID not working

- Make sure you used the **correct panel ID** from your dashboard
- The default is often `1`, but it could be different
- Check the Grafana URL when viewing the panel in edit mode

### Issue: Dashboard UID not found

- Make sure you copied the UID **correctly** from the URL
- UIDs are usually alphanumeric (letters and numbers only)
- Double-check there are no extra spaces or characters

---

## Quick Verification Checklist

- [ ] Database connection working (✅ Already verified)
- [ ] Found Grafana Dashboard UID
- [ ] Found Pie Chart Panel ID
- [ ] Updated `.env` file with UID and Panel ID
- [ ] Cleared Laravel cache
- [ ] Verified MySQL datasource in Grafana
- [ ] Verified SQL query in Grafana shows results
- [ ] Verified attendance records exist in database
- [ ] Opened http://localhost:8000 and checked staff dashboard
- [ ] Pie chart displays with data

---

## Example: Complete Setup

If your setup looks like this:

**Grafana Dashboard URL:**
```
http://localhost:3000/d/abc123def456/attendance-dashboard
```

**Pie Chart Panel ID:** `2`

**Your `.env` should be:**
```env
GRAFANA_URL=http://localhost:3000
GRAFANA_DASHBOARD_UID=abc123def456
GRAFANA_PIE_CHART_PANEL_ID=2
```

**Then this iframe will be generated in Laravel:**
```html
<iframe 
  src="http://localhost:3000/d/abc123def456/attendance-dashboard?panelId=2&var-staff_id=1&refresh=30s"
  width="100%"
  height="500"
  frameborder="0">
</iframe>
```

---

## Need More Help?

Refer to these other documentation files:
- **GRAFANA_SETUP_VISUAL_GUIDE.md** - Visual step-by-step guide
- **GRAFANA_QUICK_REFERENCE_CARD.md** - Quick commands reference
- **GRAFANA_SQL_READY_TO_USE.md** - Ready-to-use SQL queries

---

**Status:** Your database connection is now working perfectly! ✅
**Next:** Just update your .env with the Grafana UID and Panel ID, then test the dashboard.
