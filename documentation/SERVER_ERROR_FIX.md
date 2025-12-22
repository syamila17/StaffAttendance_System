# Server Error Diagnosis & Fix

## Issue Identification

Both staff and admin users are experiencing server errors after login.

### Root Cause Analysis

The issue is likely one of the following:
1. **Missing database migrations** - Tables don't exist
2. **Session table issues** - StaffSession table missing
3. **Incorrect database connection** - Can't connect to MySQL
4. **Missing .env file** - Application not properly configured

---

## Quick Fixes (In Order)

### Fix 1: Run Database Migrations (MOST LIKELY)

```powershell
cd staff_attendance

# Clear any cached configurations
php artisan config:cache
php artisan cache:clear

# Run migrations
php artisan migrate

# If that fails, use force
php artisan migrate --force
```

### Fix 2: Check if .env File Exists

```powershell
# Check if .env exists
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
}

# Generate app key
php artisan key:generate
```

### Fix 3: Clear All Caches

```powershell
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan storage:link
```

### Fix 4: Verify Database Connection

```powershell
# Test database connection
php artisan tinker
# Then type:
DB::connection()->getPdo();
# Should return connection object
```

---

## Step-by-Step Solution

### Step 1: Navigate to Project
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
```

### Step 2: Set Up Environment
```powershell
# Copy environment file if missing
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host ".env file created"
}

# Generate application key
php artisan key:generate
```

### Step 3: Run Migrations
```powershell
# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status
```

### Step 4: Create Symlink for Storage
```powershell
php artisan storage:link
```

### Step 5: Clear Caches
```powershell
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 6: Restart Laravel Server
```powershell
# Stop current server (Ctrl+C)
# Then restart
php artisan serve
```

---

## Expected Tables

These tables should exist after running migrations:

- [ ] `staff` - Staff member records
- [ ] `admin` - Admin user records
- [ ] `staff_sessions` - Session tracking
- [ ] `attendance` - Attendance records
- [ ] `leave_requests` - Leave request records
- [ ] `departments` - Department records
- [ ] `teams` - Team records
- [ ] `staff_profiles` - Staff profile information

### Verify Tables Exist

```powershell
# Connect to MySQL
docker exec -it staffattendance_system-mysql-1 mysql -u root -p

# In MySQL prompt:
USE staffAttend_data;
SHOW TABLES;

# Should see all tables listed above
```

---

## Common Error Solutions

### Error: "SQLSTATE[HY000]: General error: 1030"
**Solution:** Run migrations
```powershell
php artisan migrate --fresh
```

### Error: "Class 'StaffSession' not found"
**Solution:** Make sure StaffSession model exists and table is created
```powershell
php artisan migrate
```

### Error: "No database selected"
**Solution:** Check .env DATABASE_* variables
```
DATABASE_HOST=localhost  (or mysql)
DATABASE_PORT=3307
DATABASE_NAME=staffAttend_data
DATABASE_USER=root
DATABASE_PASSWORD=your_password
```

### Error: "Session store is not set"
**Solution:** Ensure session configuration is correct
```powershell
php artisan config:publish
```

---

## Debug Mode (If Still Having Issues)

### Enable Debug Mode
1. Open `.env`
2. Change: `APP_DEBUG=true`
3. Restart server
4. Check error messages for specific issues

### Check Application Log
```powershell
# View Laravel log
Get-Content storage/logs/laravel.log -Tail 50

# Or live monitor
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

### Test Login Manually

```powershell
php artisan tinker

# Test staff login
$staff = App\Models\Staff::where('staff_email', 'test@example.com')->first();
echo $staff ? 'Staff found' : 'Staff not found';

# Test admin login
$admin = App\Models\Admin::where('admin_email', 'admin@example.com')->first();
echo $admin ? 'Admin found' : 'Admin not found';
```

---

## Complete Reset (If Nothing Else Works)

```powershell
# Back up database first!
docker exec staffattendance_system-mysql-1 mysqldump -u root -p staffAttend_data > backup.sql

# Clear everything
php artisan migrate:refresh

# Recreate database structure
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear

# Restart
php artisan serve
```

---

## Verification Checklist

After applying fixes, verify:

- [ ] Laravel server starts without errors
- [ ] Can access http://localhost:8000/login
- [ ] Can enter staff credentials
- [ ] After login, redirects to /staff_dashboard
- [ ] Staff dashboard loads without errors
- [ ] Can access http://localhost:8000/admin_login
- [ ] Can enter admin credentials
- [ ] After login, redirects to /admin_dashboard
- [ ] Admin dashboard loads without errors

---

## Troubleshooting Order

1. **First:** Run `php artisan migrate`
2. **Second:** Create/update `.env` file
3. **Third:** Clear all caches
4. **Fourth:** Check database connection
5. **Fifth:** Enable debug mode and check logs
6. **Last Resort:** Complete database refresh

---

**Status:** Ready to diagnose and fix  
**Next Step:** Run migration command above
