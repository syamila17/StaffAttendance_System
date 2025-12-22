# CRITICAL FIX - Staff & Admin Server Errors

## The Problem

Both staff and admin users get server errors after successful login.

**Root Cause:** Missing database migrations OR misconfigured session

---

## Solution Steps (Run in Order)

### STEP 1: Verify Database & Create .env
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance

# Check if .env exists
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "✅ .env created"
}
```

### STEP 2: Generate Application Key
```powershell
php artisan key:generate
# Output should show: Application key [base64:...] set successfully.
```

### STEP 3: Check Database Configuration

Edit `.env` file and verify:
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3307
DB_DATABASE=staffAttend_data
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### STEP 4: Run Database Migrations
```powershell
# Clear config cache first
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate

# You should see output like:
# Migrating: 2025_01_01_000001_create_staff_table.php
# Migrated: 2025_01_01_000001_create_staff_table.php
```

### STEP 5: Verify All Tables Exist
```powershell
# Test database connection
php artisan tinker

# Check if tables exist
Schema::hasTable('staff')
Schema::hasTable('admin')
Schema::hasTable('staff_sessions')
Schema::hasTable('attendance')

# Exit tinker
exit
```

### STEP 6: Create Storage Symlink
```powershell
php artisan storage:link
```

### STEP 7: Clear All Caches
```powershell
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### STEP 8: Restart Laravel Server
```powershell
# Stop existing server (Ctrl+C if running)
# Then restart
php artisan serve
```

---

## Verify the Fix

1. **Open browser:** http://localhost:8000/login
2. **Test staff login:**
   - Email: `staff@example.com` (or your staff email)
   - Password: `password`
   - Should redirect to: http://localhost:8000/staff_dashboard
   - ✅ Dashboard should load WITHOUT errors

3. **Test admin login:**
   - URL: http://localhost:8000/admin_login
   - Email: `admin@example.com` (or your admin email)
   - Password: `password`
   - Should redirect to: http://localhost:8000/admin_dashboard
   - ✅ Dashboard should load WITHOUT errors

---

## If Still Getting Error

### Enable Debug Mode
1. Open `.env`
2. Change: `APP_DEBUG=true`
3. Save
4. Try login again - should show detailed error

### Check Laravel Log
```powershell
# View last 50 lines of error log
Get-Content storage/logs/laravel.log -Tail 50
```

### Common Errors & Fixes

| Error | Fix |
|-------|-----|
| `SQLSTATE[HY000]: General error: 1030` | Run: `php artisan migrate` |
| `Class 'StaffSession' not found` | Run: `php artisan migrate` |
| `PDOException: SQLSTATE[08006]` | Check DB_HOST in .env (should be 'localhost' or 'mysql') |
| `No database selected` | Check DB_DATABASE in .env (should be 'staffAttend_data') |
| `Session store is not set` | Check SESSION_DRIVER in .env (should be 'file') |

---

## Nuclear Option (Complete Reset)

If nothing works, do this:

```powershell
# BACKUP FIRST!
docker exec staffattendance_system-mysql-1 mysqldump -u root -p staffAttend_data > backup_$(Get-Date -Format yyyyMMdd_HHmmss).sql

# Then reset everything
php artisan migrate:reset
php artisan migrate

# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart
php artisan serve
```

---

## File Corrections Made

✅ Fixed double semicolon in `app/Models/Admin.php` (line 6)  
✅ Fixed HTML structure in `staff_dashboard.blade.php`  
✅ Verified all route names are correct

---

## Expected Result After Fix

- ✅ Staff can login and see dashboard
- ✅ Admin can login and see dashboard
- ✅ No server errors (500 errors)
- ✅ All pages load correctly
- ✅ Charts and statistics display
- ✅ Database queries work

---

**Status:** Ready to apply fixes  
**Priority:** HIGH - Users cannot access system  
**Time to Fix:** 5 minutes

Run STEP 1 through STEP 8 above to fix the issue.
