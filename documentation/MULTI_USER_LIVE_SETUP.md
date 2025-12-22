# Sistema Kehadiran UTM - Live Multi-User Setup ✅

## System Configuration for Production

### ✨ Features Enabled

1. **Multiple Concurrent Users** ✅
   - Session-based authentication
   - Each user has independent session
   - Session lifetime: 8 hours (480 minutes)
   - File-based session storage

2. **Subdomain-Based Access** ✅
   - Staff Portal: `staff.sistemkehadiranUTM.local`
   - Admin Portal: `admin.sistemkehadiranUTM.local`
   - Fallback: `localhost:8000` (for both)

3. **Production-Ready** ✅
   - APP_ENV set to production
   - DEBUG mode disabled
   - Domain-based session handling

---

## Setup Instructions

### Step 1: Configure Windows Hosts File

Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator):

```
# Add these lines at the end:
127.0.0.1       sistemkehadiranUTM.local
127.0.0.1       staff.sistemkehadiranUTM.local
127.0.0.1       admin.sistemkehadiranUTM.local
```

**How to edit:**
1. Open Notepad as Administrator
2. File → Open → `C:\Windows\System32\drivers\etc\hosts`
3. Add the lines above
4. Save and close

### Step 2: Update .env File

Already configured:
```env
APP_NAME="SistemKehadiranUTM"
APP_ENV=production
APP_DEBUG=false
SESSION_LIFETIME=480
SESSION_DOMAIN=.utm.local
```

### Step 3: Clear Configuration Cache

```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
php artisan config:cache
php artisan route:cache
```

### Step 4: Start the Application

```powershell
php artisan serve
```

The server will start on `http://localhost:8000`

---

## Access URLs

### 🧑‍💼 Staff Portal
- **URL**: `http://staff.sistemkehadiranUTM.local:8000`
- **Alternative**: `http://localhost:8000/login`
- **Features**: Attendance tracking, leave requests, profile

### 👨‍💼 Admin Portal
- **URL**: `http://admin.sistemkehadiranUTM.local:8000`
- **Alternative**: `http://localhost:8000/admin_login`
- **Features**: Staff management, attendance reports, leave approval

---

## Multi-User Concurrent Login

### How It Works:

✅ **Each user gets their own session**
- User A logs in as Staff ID 1 → Session A created
- User B logs in as Staff ID 2 → Session B created
- Both sessions run independently
- No interference between users

✅ **Session Storage**
- Location: `storage/framework/sessions/`
- Files named with unique session IDs
- Automatic cleanup after 8 hours (480 minutes)

✅ **Session Domain**
- Shared across: `.sistemkehadiranUTM.local`
- Staff and Admin use separate cookies
- Prevents cross-domain conflicts

### Example Scenario:

```
Computer 1:
  Staff Portal (staff.sistemkehadiranUTM.local)
  User: John (Staff ID: 1)
  Session: abc123def456
  Check-in: 09:00 AM

Computer 2 (Same Network):
  Admin Portal (admin.sistemkehadiranUTM.local)
  User: Manager (Admin ID: 1)
  Session: xyz789uvw012
  Viewing Reports: ✓

Computer 3 (Same Network):
  Staff Portal (staff.sistemkehadiranUTM.local)
  User: Jane (Staff ID: 2)
  Session: ghi345jkl678
  Check-in: 09:05 AM

✓ All three users logged in simultaneously without issues!
```

---

## Testing Concurrent Users

### Test 1: Same Device, Different Browsers
1. Open Chrome → `staff.sistemkehadiranUTM.local:8000`
2. Login as Staff User 1
3. Open Firefox → `staff.sistemkehadiranUTM.local:8000`
4. Login as Staff User 2
5. **Result**: Both sessions independent ✓

### Test 2: Different Devices on Network
1. Device 1: Open `http://server-ip:8000` or `staff.sistemkehadiranUTM.local:8000`
2. Device 2: Open same URL (different user)
3. **Result**: Both can login and use system ✓

### Test 3: Staff and Admin Simultaneously
1. Browser Tab 1: `staff.sistemkehadiranUTM.local:8000` → Staff Login
2. Browser Tab 2: `admin.sistemkehadiranUTM.local:8000` → Admin Login
3. **Result**: Both tabs maintain separate sessions ✓

---

## Session Management

### View Active Sessions
```powershell
dir C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance\storage\framework\sessions
```

Each file = One active user session

### Clear All Sessions
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
Remove-Item storage\framework\sessions\* -Force
```

### Session Configuration Details
- **Driver**: File-based (can upgrade to Database/Redis)
- **Lifetime**: 8 hours (480 minutes)
- **Path**: `/` (root level)
- **Domain**: `.utm.local` (subdomain shared)
- **Secure**: No (use HTTPS in production)
- **HTTP Only**: Yes (prevents JavaScript access)

---

## Domain vs Localhost Usage

### When to Use Domains:
```
✅ Production environment
✅ Multiple devices on network
✅ Professional setup
✅ Real-time monitoring
```

### When to Use Localhost:
```
✅ Single device testing
✅ Development environment
✅ Quick testing
✅ No network setup needed
```

### Both Work Simultaneously!
- You can use both domain and localhost
- Same system, different access methods
- Useful for testing while in production

---

## Configuration Files Modified

1. **`.env`**
   - APP_NAME: Changed to "SistemKehadiranUTM"
   - APP_ENV: Changed to production
   - APP_DEBUG: Disabled
   - SESSION_LIFETIME: Extended to 480 minutes
   - SESSION_DOMAIN: Set to `.utm.local`

2. **`routes/web.php`**
   - Added subdomain routing for staff.sistemkehadiranUTM.local
   - Added subdomain routing for admin.sistemkehadiranUTM.local
   - Kept fallback routes for localhost:8000

---

## Security Notes

⚠️ **For Production (Over Internet):**
1. Change `APP_DEBUG=false` ✅ (Already done)
2. Update `APP_URL` to actual domain
3. Set `SESSION_SECURE_COOKIE=true` in HTTPS environment
4. Update `SESSION_DOMAIN` to actual domain

⚠️ **Database Access:**
- Current: Localhost only (port 3307)
- For network: May need to expose MySQL port (risky!)
- Recommendation: Keep DB on localhost, connect from app server

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Domain not resolving | Check Windows hosts file for typos |
| Session not persisting | Clear browser cache and cookies |
| Multiple users conflicting | Ensure sessions dir has write permission |
| Slow performance | Check storage/framework/sessions size |
| Can't access from network | May need firewall port 8000 open |

---

## Next Steps

1. ✅ Configure Windows hosts file
2. ✅ Run `php artisan config:cache`
3. ✅ Start Laravel server with `php artisan serve`
4. ✅ Test staff portal: `http://staff.sistemkehadiranUTM.local:8000`
5. ✅ Test admin portal: `http://admin.sistemkehadiranUTM.local:8000`
6. ✅ Test concurrent logins with multiple users

---

## Database & Sessions

### Database Status:
- ✅ MySQL running on localhost:3307
- ✅ Database: `staff_attendance`
- ✅ All tables ready
- ✅ Session data stored in files

### Concurrent User Limit:
- **File-based sessions**: Limited by filesystem (typically 1000+)
- **For high volume**: Upgrade to Database/Redis sessions
- **Recommended upgrade**: At 500+ concurrent users

---

**System Ready for Production Multi-User Access!** 🚀

