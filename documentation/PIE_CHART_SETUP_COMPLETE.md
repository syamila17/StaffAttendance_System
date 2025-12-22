# Pie Chart Setup Guide - Complete Instructions

## 🎯 Current Status

- ✅ Dashboard views updated to multilingual support (English & Malay)
- ✅ Month selector added - can view any month from last 12 months
- ✅ Current month reset button - easily return to today
- ✅ Refresh button - manually refresh Grafana pie chart
- ✅ On Leave detection - shows "On Leave" status automatically
- ✅ Date and time display with auto-update every second
- ✅ Markdown files organized in documentation folder
- ⏳ **Grafana pie chart needs to be configured**

---

## 🔧 Step-by-Step Grafana Setup

### Step 1: Access Grafana Dashboard

1. Open browser: `http://localhost:3000`
2. Login with default credentials:
   - **Username**: `admin`
   - **Password**: `admin`
3. You may be prompted to change password (optional)

### Step 2: Create New Dashboard

1. Click **"+"** icon on left sidebar
2. Select **"Create" → "Dashboard"**
3. Click **"Add Panel"** or **"Add a new panel"**
4. Select **"Pie Chart"** visualization

### Step 3: Configure MySQL Datasource

1. In panel editor, look for **"Data source"** dropdown
2. Make sure **"MySQL"** is selected
3. If not available, add it:
   - Go to **Settings** (gear icon) → **Data sources**
   - Click **"Add data source"**
   - Select **"MySQL"**
   - Enter these details:
     - **Host**: `mysql:3306` (if using Docker) or `127.0.0.1:3307` (if local)
     - **Database**: `staffAttend_data`
     - **User**: `root`
     - **Password**: `root`
   - Click **"Test"** to verify connection
   - Click **"Save & Test"**

### Step 4: Add SQL Query

Copy and paste this SQL query in the query editor:

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
AND staff_id = $__urlParams.staffId
GROUP BY CASE 
  WHEN status = 'present' THEN 'Present'
  WHEN status = 'on leave' THEN 'On Leave'
  ELSE 'Other'
END
ORDER BY status DESC;
```

### Step 5: Configure Pie Chart Display

1. Go to **"Visualization"** tab
2. Select **"Pie Chart"** (if not already selected)
3. In **"Display"** section:
   - **Pie chart type**: Select "Pie"
   - **Show legend**: Toggle ON
   - **Legend placement**: Select "Right"
   - **Show tooltips**: Toggle ON

4. In **"Value options"**:
   - **Unit**: Leave as "Short"
   - **Decimals**: Set to 0

### Step 6: Configure Colors

1. Go to **"Overrides"** tab
2. Click **"Add override"**
3. For each status, set custom colors:

   **For "Present" series:**
   - Field: `Present`
   - Color: Green (#22c55e)
   
   **For "On Leave" series:**
   - Field: `On Leave`
   - Color: Blue (#3b82f6)
   
   **For "Other" series:**
   - Field: `Other`
   - Color: Gray (#6b7280)

### Step 7: Set Variables (Important!)

1. Click **Dashboard Settings** (gear icon)
2. Go to **"Variables"** tab
3. Create new variable:
   - **Name**: `staffId`
   - **Type**: `Text box`
   - **Default value**: `1`
4. Click **"Save"**

### Step 8: Save Panel and Get UID

1. Click **"Save"** button in top-right
2. Give your dashboard a name: `attendance-dashboard`
3. Click **"Save"** again
4. **Note the UID** from the URL: `http://localhost:3000/d/[UID]/dashboard-name`
5. **Note the Panel ID** - click on the pie chart panel, it's shown in the title

### Step 9: Update Laravel Configuration

1. Open: `staff_attendance/.env`
2. Update these values:

```env
GRAFANA_DASHBOARD_UID=your_dashboard_uid_here
GRAFANA_PIE_CHART_PANEL_ID=1
```

Example:
```env
GRAFANA_DASHBOARD_UID=abc123xyz456
GRAFANA_PIE_CHART_PANEL_ID=2
```

### Step 10: Clear Cache and Test

Run these commands:

```powershell
cd 'C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance'
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Then open: `http://localhost:8000/staff_dashboard`

---

## 🧪 Testing Your Setup

### Test 1: Verify SQL Query in phpMyAdmin

1. Open: `http://localhost:8081`
2. Login: `root` / `root`
3. Select database: `staffAttend_data`
4. Go to **SQL** tab
5. Paste this query (replace 1 with actual staff ID):

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
AND staff_id = 1
GROUP BY status;
```

6. Click **Execute**
7. Should show data like:
   - Present | 15
   - On Leave | 2
   - Other | 1

### Test 2: Verify in Grafana

1. Open: `http://localhost:3000`
2. Go to your `attendance-dashboard`
3. You should see pie chart with data
4. Click on pie chart to edit
5. The SQL query should return results with green "Present" section

### Test 3: Verify in Laravel Dashboard

1. Open: `http://localhost:8000/staff_dashboard`
2. You should see pie chart embedded
3. Try these functions:
   - **Refresh button**: Click to refresh data
   - **Month selector**: Change to previous month
   - **Current button**: Return to current month
   - **Language switcher**: Toggle between English/Bahasa Melayu

---

## 🐛 Troubleshooting

### Pie Chart Shows "No Data"

**Check 1: Attendance Records Exist?**
```sql
SELECT COUNT(*) FROM attendance 
WHERE YEAR(attendance_date) = 2025 
AND MONTH(attendance_date) = 12;
```

**Check 2: Correct staffId?**
- The URL should have: `http://localhost:3000/d/[UID]/...?var-staffId=1`
- Make sure staff_id matches a real staff member

**Check 3: MySQL Datasource Connected?**
- In Grafana, go to Settings → Data sources
- Click MySQL datasource
- Click "Test" button
- Should show "Database Connection OK"

### Connection Refused Error

**Check if Grafana is running:**
```powershell
docker ps | grep grafana
```

**Check if MySQL is running:**
```powershell
docker ps | grep mysql
```

**Restart containers:**
```powershell
cd 'C:\Users\syami\Desktop\StaffAttendance_System'
docker-compose down
docker-compose up -d
```

### SQL Query Returns No Data

1. Verify attendance table has records:
   ```sql
   SELECT * FROM attendance LIMIT 5;
   ```

2. Check if records are for current month:
   ```sql
   SELECT DISTINCT YEAR(attendance_date), MONTH(attendance_date) FROM attendance;
   ```

3. Verify status values match (should be lowercase):
   ```sql
   SELECT DISTINCT status FROM attendance;
   ```

---

## 📊 Pie Chart SQL Variations

### Option 1: Only Present vs Others

```sql
SELECT 
  CASE 
    WHEN status = 'present' THEN 'Present'
    ELSE 'Not Present'
  END as status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $__urlParams.staffId
GROUP BY CASE WHEN status = 'present' THEN 'Present' ELSE 'Not Present' END;
```

### Option 2: All Statuses

```sql
SELECT 
  status,
  COUNT(*) as count
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $__urlParams.staffId
GROUP BY status;
```

### Option 3: Present Percentage

```sql
SELECT 
  'Present' as status,
  ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as percentage
FROM attendance
WHERE YEAR(attendance_date) = YEAR(NOW())
AND MONTH(attendance_date) = MONTH(NOW())
AND staff_id = $__urlParams.staffId;
```

---

## 🎨 Dashboard Colors

Recommended color scheme for pie chart:

| Status | Color | Hex Code |
|--------|-------|----------|
| Present | Green | #22c55e |
| On Leave | Blue | #3b82f6 |
| Absent | Red | #ef4444 |
| Late | Yellow | #eab308 |
| Other | Gray | #6b7280 |

---

## 📝 Language Support

Dashboard supports two languages:

- **English**: `http://localhost:8000/staff_dashboard?lang=en`
- **Bahasa Melayu**: `http://localhost:8000/staff_dashboard?lang=ms`

Or use the language switcher in the sidebar!

---

## ✅ Verification Checklist

- [ ] Grafana running on port 3000
- [ ] MySQL datasource configured
- [ ] Pie chart panel created
- [ ] SQL query tested and returns data
- [ ] Dashboard UID noted and added to .env
- [ ] Panel ID noted and added to .env
- [ ] Laravel cache cleared
- [ ] Dashboard accessible at http://localhost:8000/staff_dashboard
- [ ] Pie chart displays with data
- [ ] Month selector works
- [ ] Refresh button works
- [ ] Language switcher works
- [ ] On Leave status shows correctly

---

## 📚 Additional Resources

- **GRAFANA_PIE_CHART_SQL.md** - SQL query examples and variations
- **GRAFANA_SETUP_VISUAL_GUIDE.md** - Visual step-by-step guide with screenshots
- **GRAFANA_QUICK_REFERENCE_CARD.md** - Quick command reference

For more help, check the other documentation files in this folder!
