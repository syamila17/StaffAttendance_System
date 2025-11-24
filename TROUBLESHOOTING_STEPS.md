# 🚀 FINAL TROUBLESHOOTING - Step by Step

## IF YOU'RE STILL STUCK WITH REDIRECT LOOPS

Follow these exact steps:

---

## Step 1: Stop Everything
```powershell
# Stop Laravel if running (Ctrl+C in terminal)
# Stop Docker/MySQL if needed:
docker-compose down
```

---

## Step 2: Clean Everything
```powershell
cd staff_attendance

# Delete all session files
Remove-Item storage/framework/sessions/* -Force -Confirm:$false

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Step 3: Verify Configuration
```powershell
# Check .env
cat .env | Select-String SESSION_DRIVER
# Should show: SESSION_DRIVER=file

# Check config
grep -n "driver.*=" config/session.php | Select-Object -First 1
# Should show: 'driver' => env('SESSION_DRIVER', 'file'),
```

---

## Step 4: Start Fresh
```powershell
# Start Docker
docker-compose up -d

# Wait for MySQL to start (5-10 seconds)

# Start Laravel
php artisan serve
```

---

## Step 5: Test Access
**In new browser window (or incognito):**

1. Visit: `http://localhost:8000/test-simple`
   - Should show: "✅ Simple test works!"
   - If yes → go to step 6
   - If NO → Laravel not responding

2. Visit: `http://localhost:8000/`
   - Should show: home page with login links
   - If yes → go to step 6
   - If NO → routing broken

3. Visit: `http://localhost:8000/login`
   - Should show: login form
   - If YES ✅ → go to step 6
   - If redirect loop → check error log

---

## Step 6: Login
**Enter these credentials:**
```
Email: ahmad@utm.edu.my
Password: password123
```

**What should happen:**
- Form submits
- Redirects to `/staff_dashboard`
- Dashboard displays

**If redirect loop:**
1. Check browser console (F12 → Console) for errors
2. Check Laravel log: `tail -f storage/logs/laravel.log`
3. Try different browser
4. Try incognito mode

---

## Step 7: If Still Stuck

### Check Laravel is Really Running
```powershell
# In PowerShell:
netstat -ano | findstr :8000

# Should show something like:
# TCP  0.0.0.0:8000  LISTENING  12345
```

### Check MySQL is Running
```powershell
# Test database connection
php artisan migrate:status

# Should show list of migrations
```

### Check Sessions Directory
```powershell
# Verify folder exists and has files
Get-ChildItem storage\framework\sessions\ | Measure-Object

# Should show Count: 1 or more
```

### Check for PHP Errors
```powershell
php -l app/Http/Controllers/AuthController.php
php -l app/Http/Middleware/StaffAuth.php
php -l routes/web.php

# All should show "No syntax errors"
```

---

## 🆘 NUCLEAR OPTIONS

### Option A: Completely Reset
```powershell
cd staff_attendance

# Stop everything
docker-compose down

# Delete sessions
Remove-Item storage\framework\sessions\* -Force -Confirm:$false

# Clear everything
php artisan cache:clear
php artisan config:clear  
php artisan view:clear

# Restart
docker-compose up -d
Start-Sleep -Seconds 5
php artisan serve
```

### Option B: Fresh Database
```powershell
# Reload database
php artisan migrate:fresh --seed

# This will:
# - Drop all tables
# - Re-run all migrations
# - Seed with test data
```

### Option C: Different Port
```powershell
# Try port 3000 instead of 8000
php artisan serve --port=3000

# Then visit: http://localhost:3000/login
```

---

## 📋 Checklist Before Giving Up

- [ ] Docker is running
- [ ] MySQL can be accessed
- [ ] Laravel serves (no crash on `php artisan serve`)
- [ ] Cache is cleared
- [ ] Sessions directory exists and is writable
- [ ] Browser cookies deleted
- [ ] Hard refresh done (Ctrl+Shift+R)
- [ ] Tried different browser
- [ ] Tried incognito mode
- [ ] Checked Laravel log for errors
- [ ] Verified .env SESSION_DRIVER=file
- [ ] Checked config/session.php driver setting

---

## 🎯 Expected Flow

```
1. You visit: http://localhost:8000/login
   ↓
2. AuthController::showLoginForm() runs
   ↓
3. Login view displays (STOPS - no more redirects) ✅
   ↓
4. You enter credentials and click Login
   ↓
5. Form POSTs to /login
   ↓
6. AuthController::login() validates
   ↓
7. session()->put('staff_id', ...) runs
   ↓
8. Redirects to staff.dashboard
   ↓
9. StaffAuth middleware checks: session has staff_id? YES ✅
   ↓
10. Dashboard displays
```

**No loops. Clean flow.**

---

## 🔧 Configuration Summary

| Setting | Value | Location |
|---------|-------|----------|
| SESSION_DRIVER | file | .env |
| Session driver | file | config/session.php:21 |
| Guest middleware | DISABLED | app/Http/Kernel.php |
| Home route | view('home') | routes/web.php:46 |
| Login route | no middleware | routes/web.php:40-41 |

---

## 💡 Tips

- **Test URLs first** - they tell you what's broken
- **Check Laravel log** - `storage/logs/laravel.log`
- **Browser DevTools** - F12 → Console for JavaScript errors
- **Incognito mode** - avoids cache issues
- **Fresh terminal** - clear terminal and start new session
- **Check ports** - ensure 8000 is available

---

## ✅ When It Works

You'll see:
- Login form loads (no redirect)
- Can enter credentials
- Redirects to dashboard
- Dashboard shows
- Can logout

---

**Try these steps now and let me know which test route fails, if any!**
