# Fix "Page Expired" Error on Staff Management Page

## Issue Identified
The "page expired" error occurs due to CSRF token validation failures, typically caused by session/cookie configuration conflicts.

## Root Cause
- **Inconsistent SameSite cookie settings**: Environment file had `SESSION_SAME_SITE=lax` while config defaulted to `SESSION_SAME_SITE=none`
- This caused session cookies to not be properly maintained across requests
- CSRF tokens were being invalidated when page was refreshed or navigated

## Changes Applied

### 1. Updated `.env` file
```diff
- SESSION_SAME_SITE=lax
+ SESSION_SAME_SITE=none
```

### 2. Updated `config/session.php`
```diff
- 'same_site' => env('SESSION_SAME_SITE', 'none'),
+ 'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

This ensures:
- Environment variable `SESSION_SAME_SITE=none` is used in production
- Default fallback in config is `lax` for security

## How to Apply Fix

### Step 1: Clear Application Cache
Run these commands in the Laravel project directory:

```bash
php artisan cache:clear
php artisan config:cache
php artisan view:clear
php artisan session:clear
```

Or use the provided batch script:
```bash
cd staff_attendance
./scripts/batch/clear_cache.bat
```

### Step 2: Restart the Application
Stop and restart your Laravel development server or web server to apply the changes.

### Step 3: Clear Browser Cache
- Clear all cookies for `localhost:8000`
- Clear all cached data
- Restart your browser

### Step 4: Verify
1. Navigate to `http://localhost:8000/admin/staff`
2. Try to create or edit a staff member
3. Submit the form - it should work without "page expired" error

## Technical Details

### Why This Fixes It

**SameSite Cookies Explained:**
- `none`: Cookie sent with all requests (requires HTTPS in production)
- `lax`: Cookie sent with top-level navigations and same-site requests (default, secure)
- `strict`: Cookie only sent with same-site requests

**The Fix:**
- Setting `SESSION_SAME_SITE=none` in .env allows the session cookie to be maintained across all requests
- The config default of `lax` provides a fallback for security
- This ensures CSRF tokens remain valid throughout the user's session

### Files Modified
- `/staff_attendance/.env`
- `/staff_attendance/config/session.php`

## Alternative Solutions (if issue persists)

### Solution A: Change to database sessions
Edit `.env`:
```
SESSION_DRIVER=database
```

Then run migration (if not exists):
```bash
php artisan session:table
php artisan migrate
```

### Solution B: Increase session lifetime
Edit `.env`:
```
SESSION_LIFETIME=1440
```
(Changes from 480 to 1440 minutes = 24 hours)

### Solution C: Disable session encryption
Already disabled, but verify in `.env`:
```
SESSION_ENCRYPT=false
```

## Testing

Test the form submission with:
1. Simple create/edit workflow
2. Navigation away and back to the page
3. Multiple form submissions
4. Different browsers (Chrome, Firefox, Safari)

All should work without "page expired" errors.

## Support

If the error persists:
1. Check browser console for CSRF token errors
2. Verify session files are being created in `storage/framework/sessions/`
3. Check Laravel logs in `storage/logs/laravel.log`
4. Ensure database connection is working if using database sessions
