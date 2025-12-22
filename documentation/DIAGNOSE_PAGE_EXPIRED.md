# Diagnosis: Staff "Page Expired" Error

## Issue Description
Staff management pages are showing "Page Expired" (HTTP 419) errors when submitting forms.

## Possible Causes
1. **Session files not being created** - Session directory permissions
2. **CSRF token mismatch** - Token regeneration issues
3. **Session driver misconfiguration** - File vs database
4. **Middleware execution order** - Token regeneration timing
5. **Browser cookie issues** - Session cookie not being set
6. **Application cache** - Stale middleware configuration

## Current Configuration Status

### ✅ .env Settings
- SESSION_DRIVER=file ✓
- SESSION_LIFETIME=1440 ✓
- SESSION_EXPIRE_ON_CLOSE=false ✓
- SESSION_ENCRYPT=false ✓
- SESSION_SAME_SITE=lax ✓
- SESSION_HTTP_ONLY=true ✓
- SESSION_COOKIE=staff_attendance_session ✓

### ✅ Middleware Stack (app/Http/Kernel.php)
1. EncryptCookies
2. AddQueuedCookiesToResponse
3. StartSession
4. **EnsureSessionIntegrity** (checks session directory)
5. ShareErrorsFromSession
6. **VerifyCsrfToken** (verifies token)
7. SubstituteBindings
8. **RegenerateToken** (regenerates token safely) ✓ FIXED

### ✅ CSRF Token in Forms
- staff_create.blade.php has @csrf ✓
- staff_edit.blade.php has @csrf ✓

## Next Steps to Try

### 1. Check Session Directory Exists
```powershell
cd c:\Users\syami\Desktop\StaffAttendance_system\staff_attendance
Test-Path storage\framework\sessions
# Should return True
```

### 2. Verify Middleware is Loaded
```bash
php artisan tinker
>>> Cache::tags(['app:middleware'])->flush()  // Clear middleware cache
>>> exit()
```

### 3. Create a Test Session
```bash
php artisan tinker
>>> session()->put('test', 'value')
>>> session()->get('test')  // Should return 'value'
>>> exit()
```

### 4. Test Browser Session Cookie
1. Open admin panel: http://localhost:8000/admin_login
2. Open DevTools (F12)
3. Go to Application → Cookies
4. Look for `staff_attendance_session` cookie
5. Verify it has a value (not empty)

### 5. Check Session File Created
After login, run:
```powershell
Get-ChildItem storage\framework\sessions | Measure-Object -Line
# Should show session files created
```

## What to Look For

### In Browser Console (F12)
- No CSRF token errors
- No 419 errors before form submission
- No JavaScript errors

### In Server Logs
```bash
tail -f storage/logs/laravel.log
# Look for:
# - CSRF token mismatch
# - Session errors
# - Middleware errors
```

### Session File Issues
- Check: `storage/framework/sessions/` has files ✓
- Check: Files have recent timestamps ✓
- Check: Read/write permissions ✓

## If Issues Persist

### Option A: Switch to Database Sessions
```env
SESSION_DRIVER=database
```
Then create table:
```bash
php artisan session:table
php artisan migrate
```

### Option B: Force Session Regeneration
Add to `.env`:
```env
SESSION_REGENERATE_AFTER_LOGIN=true
```

### Option C: Increase Session Lifetime for Testing
```env
SESSION_LIFETIME=2880  # 48 hours for testing
```

### Option D: Disable CSRF for Testing (NOT RECOMMENDED)
Only temporarily to identify if it's CSRF vs session issue:
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    '/admin/staff*',  // Temporarily
];
```

## Verification Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test configuration
php artisan tinker
>>> config('session')  // View all session settings

# Restart server
php artisan serve
```

---

**Status**: Ready for diagnosis
**Date**: December 4, 2025
