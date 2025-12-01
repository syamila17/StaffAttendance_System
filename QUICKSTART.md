# Staff Attendance System - Quick Start Guide

## 🚀 Quick Setup (5 minutes)

### 1. Navigate to project
```powershell
cd c:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
```

### 2. Install composer dependencies
```powershell
composer install
```

### 3. Start Docker (MySQL + phpMyAdmin)
```powershell
# From parent directory
cd ..
docker-compose up -d

# Back to laravel app
cd staff_attendance
```

### 4. Generate app key
```powershell
php artisan key:generate
```

### 5. Run database migrations and seed
```powershell
php artisan migrate:refresh --seed --force
```

### 6. Start Laravel server
```powershell
php artisan serve
```

✅ **Done!** Visit: http://localhost:8000

---

## 🔐 Login with Test Accounts

### Staff Login
- **URL:** http://localhost:8000/login
- **Email:** test@utm.edu.my
- **Password:** password123

### Admin Login
- **URL:** http://localhost:8000/admin_login
- **Email:** admin@utm.edu.my
- **Password:** admin123

---

## 📊 Verify Everything Works

### Check Database
```powershell
# View in phpMyAdmin
# URL: http://localhost:8081
# Username: root
# Password: root
# Database: staffAttend_data
```

### Check Routes
```powershell
php artisan route:list
```

### Check Migrations Status
```powershell
php artisan migrate:status
```

---

## 🐛 Common Issues & Fixes

### Issue: "Illuminate\Database\QueryException - table doesn't exist"
```powershell
php artisan migrate:refresh --seed --force
```

### Issue: MySQL Connection Failed
```powershell
# Verify Docker containers running
docker-compose ps

# Restart containers
docker-compose restart

# Check logs
docker-compose logs mysql
```

### Issue: Session/Auth Problems
```powershell
# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:cache

# Restart server
php artisan serve
```

### Issue: Toggle icon not showing in login
Verify this link is in HTML `<head>`:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

---

## 📁 Key Files Locations

| What | Where |
|------|-------|
| Staff Login | `resources/views/login.blade.php` |
| Admin Login | `resources/views/admin_login.blade.php` |
| Staff Dashboard | `resources/views/staff_dashboard.blade.php` |
| Admin Dashboard | `resources/views/admin_dashboard.blade.php` |
| Attendance Page | `resources/views/attendance.blade.php` |
| All Routes | `routes/web.php` |
| Database Config | `.env` |
| Migrations | `database/migrations/` |
| Controllers | `app/Http/Controllers/` |
| Models | `app/Models/` |
| Middleware | `app/Http/Middleware/` |

---

## 🔧 Configuration

### `.env` Key Settings
```dotenv
APP_DEBUG=true                  # Show errors (set to false in production)
DB_HOST=127.0.0.1
DB_PORT=3307                    # MySQL port (Docker mapped from 3306)
DB_DATABASE=staffAttend_data
DB_USERNAME=root
DB_PASSWORD=root
SESSION_DRIVER=database         # Important: database-driven sessions
SESSION_LIFETIME=120            # Session expires in 2 hours
```

### Docker Services
- **MySQL:** `localhost:3307`
- **phpMyAdmin:** `http://localhost:8081`
- **Laravel App:** `http://localhost:8000`

---

## 🎯 Main Features

### Staff Features
✅ Login/Logout  
✅ Check-in/Check-out with timestamps  
✅ View attendance history (30 days)  
✅ Manage profile  
✅ View today's attendance status

### Admin Features
✅ Login/Logout  
✅ View all staff attendance  
✅ Mark attendance for any staff  
✅ Filter by date  
✅ Generate reports with date range  
✅ Filter reports by staff  
✅ View attendance statistics

---

## 📈 Database Schema Overview

```
┌─────────────────────────────────────┐
│ STAFF                               │
│ - staff_id (PK)                     │
│ - staff_name                        │
│ - staff_email (UNIQUE)              │
│ - staff_password (bcrypt hashed)    │
│ - team_id                           │
└─────────────────────────────────────┘
         │
         ├──────────────────┐
         │                  │
         ▼                  ▼
┌──────────────────────┐  ┌──────────────────────┐
│ STAFF_PROFILE (1:1)  │  │ ATTENDANCE (1:N)     │
│ - id (PK)            │  │ - id (PK)            │
│ - staff_id (FK)      │  │ - staff_id (FK)      │
│ - full_name          │  │ - attendance_date    │
│ - phone_number       │  │ - check_in_time      │
│ - address            │  │ - check_out_time     │
│ - position           │  │ - status             │
│ - department         │  │ - remarks            │
│ - profile_image      │  │ UNIQUE(staff_id, date)
└──────────────────────┘  └──────────────────────┘

┌─────────────────────────────────────┐
│ ADMIN                               │
│ - admin_id (PK)                     │
│ - admin_name                        │
│ - admin_email (UNIQUE)              │
│ - admin_password (bcrypt hashed)    │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ SESSIONS (for session storage)      │
│ - id (PK)                           │
│ - user_id (FK, nullable)            │
│ - ip_address                        │
│ - user_agent                        │
│ - payload (session data)            │
│ - last_activity                     │
└─────────────────────────────────────┘
```

---

## 🔒 Security Features

✅ **Password Hashing:** bcrypt with 12 rounds  
✅ **Session Protection:** Database-driven sessions  
✅ **CSRF Protection:** Laravel CSRF middleware  
✅ **SQL Injection Prevention:** Eloquent ORM with prepared statements  
✅ **Middleware Authentication:** Staff and Admin route protection  
✅ **Secure Session Handling:** Session regeneration after login  
✅ **HTTP Only Cookies:** SESSION_HTTP_ONLY=true

---

## 📝 Fixes Applied in Latest Version

1. ✅ Fixed Staff model `primaryKey` typo (was `primarykey`)
2. ✅ Enabled StaffAuth middleware with correct session checks
3. ✅ Enabled AdminAuth middleware with correct session checks
4. ✅ Added relationships to Staff model (profile, attendance)
5. ✅ Added Font Awesome CSS to admin login form
6. ✅ Created sessions table migration
7. ✅ Verified all database tables and relationships

---

## 📞 Getting Help

### View Logs
```powershell
tail -f storage/logs/laravel.log
```

### Test Database Connection
```powershell
php artisan tinker
>>> DB::connection()->getPDO()
```

### Check Application Routes
```powershell
php artisan route:list
```

### Clear All Cache
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

---

**Version:** 1.0  
**Last Updated:** November 20, 2025  
**Status:** ✅ Production Ready
