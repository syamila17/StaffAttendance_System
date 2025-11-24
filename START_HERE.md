# 🚀 START THE SYSTEM - SIMPLE GUIDE

## Quick Start (Copy & Paste)

**In PowerShell, run this:**

```powershell
cd "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"; php artisan serve --host=0.0.0.0 --port=8000
```

**Then visit:** `http://localhost:8000/login`

---

## If Above Doesn't Work

**Run this FIRST:**

```powershell
cd "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"
php artisan cache:clear --force
php artisan route:clear --force
php artisan view:clear --force
php artisan config:clear --force
php artisan serve --host=0.0.0.0 --port=8000
```

---

## Test Credentials

| Role  | Email | Password |
|-------|-------|----------|
| Staff | ahmad@utm.edu.my | password123 |
| Staff | siti@utm.edu.my | password123 |
| Admin | admin@utm.edu.my | admin123 |

---

## Important ⚠️

- ✅ Keep PowerShell window OPEN while server runs
- ✅ Press `Ctrl+C` to stop the server
- ✅ Database (MySQL) must be running
- ✅ Use `http://` NOT `https://`

---

## What You Should See

✅ **Login Form** - Email and password fields, UTM logo
❌ **Raw PHP Code** - Server not running
❌ **404 Not Found** - Wrong URL or port

---

## Troubleshooting

**"Command php not found"**
- Install PHP or add to PATH

**"Port 8000 already in use"**
- Use different port: `php artisan serve --port=3000`

**"Database connection error"**
- Check MySQL is running on port 3307
- Docker: `docker-compose up -d`

**Still seeing raw PHP code?**
- Make sure PowerShell terminal shows "Server running on [http://0.0.0.0:8000]"
- Hard refresh browser: Ctrl+Shift+R
- Clear browser cache: Ctrl+Delete → Cookies and cached images

---

## Next Steps After Login

1. Staff login → Staff Dashboard
2. Admin login → Admin Dashboard
3. Mark attendance, view reports, manage staff

Done! System is fully configured and ready to use.
