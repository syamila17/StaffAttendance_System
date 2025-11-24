# Staff Attendance System - Complete Documentation

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Project Structure](#project-structure)
4. [Database Architecture](#database-architecture)
5. [Authentication System](#authentication-system)
6. [Installation & Setup](#installation--setup)
7. [API Routes & Endpoints](#api-routes--endpoints)
8. [User Roles & Permissions](#user-roles--permissions)
9. [File Structure Guide](#file-structure-guide)
10. [Troubleshooting](#troubleshooting)

---

## 📖 Project Overview

**Staff Attendance System** is a Laravel-based web application designed to manage staff attendance tracking. It provides two distinct user roles:
- **Staff Members**: Can track their own attendance with check-in/check-out functionality
- **Administrators**: Can manage all staff attendance records and generate reports

### Key Features:
- ✅ Secure login system with password hashing (bcrypt)
- ✅ Real-time attendance tracking with timestamps
- ✅ Staff profile management
- ✅ Admin dashboard with attendance statistics
- ✅ Attendance reports with date range filtering
- ✅ Database-driven session management
- ✅ Responsive UI with Tailwind CSS

---

## 🛠 Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | 12.37.0 |
| PHP | PHP | 8.4.14 |
| Database | MySQL | 8.0 |
| CSS Framework | Tailwind CSS | Latest |
| Icons | Font Awesome | 6.4.0 |
| Templating | Blade | Laravel Default |
| Session Storage | Database (MySQL) | - |
| Docker | Docker Compose | Latest |

---

## 📁 Project Structure

```
staff_attendance/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Staff login/logout
│   │   │   ├── AdminController.php         # Admin login/dashboard
│   │   │   ├── StaffController.php         # Staff dashboard
│   │   │   ├── AttendanceController.php    # Staff attendance check-in/out
│   │   │   ├── AdminAttendanceController.php # Admin attendance management
│   │   │   ├── StaffProfileController.php  # Staff profile management
│   │   │   └── Controller.php              # Base controller
│   │   ├── Middleware/
│   │   │   ├── StaffAuth.php               # Staff route protection
│   │   │   └── AdminAuth.php               # Admin route protection
│   │   └── Kernel.php                      # Middleware registration
│   └── Models/
│       ├── Staff.php                       # Staff ORM model
│       ├── Admin.php                       # Admin ORM model
│       ├── Attendance.php                  # Attendance records ORM
│       └── StaffProfile.php                # Staff profile ORM
├── database/
│   ├── migrations/
│   │   ├── 2025_01_01_000000_create_sessions_table.php
│   │   ├── 2025_11_19_000001_create_staff_table.php
│   │   ├── 2025_11_19_000003_hash_staff_passwords.php
│   │   ├── 2025_11_19_000005_create_staff_profile_table.php
│   │   ├── 2025_11_19_000006_create_admin_table.php
│   │   └── 2025_11_19_000007_create_attendance_table.php
│   └── seeders/
│       └── DatabaseSeeder.php              # Test data seeding
├── resources/
│   └── views/
│       ├── login.blade.php                 # Staff login form
│       ├── admin_login.blade.php           # Admin login form
│       ├── staff_dashboard.blade.php       # Staff main page
│       ├── admin_dashboard.blade.php       # Admin main page
│       ├── attendance.blade.php            # Staff attendance tracking
│       ├── profile.blade.php               # Staff profile page
│       └── admin/
│           ├── attendance.blade.php        # Admin attendance management
│           └── attendance-report.blade.php # Admin attendance reports
├── routes/
│   └── web.php                             # All application routes
├── public/
│   └── index.php                           # Application entry point
├── config/
│   └── database.php                        # Database configuration
├── .env                                    # Environment variables
├── docker-compose.yml                      # Docker configuration
└── composer.json                           # PHP dependencies
```

---

## 🗄 Database Architecture

### 1. **Staff Table** (`staff`)
Stores staff member information.

```sql
CREATE TABLE staff (
    staff_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    staff_name VARCHAR(255) NOT NULL,
    staff_email VARCHAR(255) UNIQUE NOT NULL,
    staff_password VARCHAR(255) NOT NULL (hashed with bcrypt),
    team_id BIGINT UNSIGNED NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Relationships:**
- `1:1` with `staff_profile` (one staff has one profile)
- `1:N` with `attendance` (one staff has many attendance records)

---

### 2. **Admin Table** (`admin`)
Stores administrator credentials.

```sql
CREATE TABLE admin (
    admin_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    admin_name VARCHAR(255) NOT NULL,
    admin_email VARCHAR(255) UNIQUE NOT NULL,
    admin_password VARCHAR(255) NOT NULL (hashed with bcrypt),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 3. **Staff Profile Table** (`staff_profile`)
Extended staff information for profile management.

```sql
CREATE TABLE staff_profile (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    staff_id BIGINT UNIQUE NOT NULL,
    full_name VARCHAR(255) NULLABLE,
    email VARCHAR(255) NULLABLE,
    phone_number VARCHAR(20) NULLABLE,
    address TEXT NULLABLE,
    position VARCHAR(255) NULLABLE,
    department VARCHAR(255) NULLABLE,
    profile_image VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
);
```

---

### 4. **Attendance Table** (`attendance`)
Records daily attendance with check-in/check-out times.

```sql
CREATE TABLE attendance (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    staff_id BIGINT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    check_in_time TIME NULLABLE,
    check_out_time TIME NULLABLE,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'absent',
    remarks TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (staff_id, attendance_date),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
);
```

**Status Values:**
- `present` - Staff checked in on time
- `absent` - Staff did not check in
- `late` - Staff checked in late
- `leave` - Staff took approved leave

---

### 5. **Sessions Table** (`sessions`)
Database-driven session storage for user authentication.

```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULLABLE,
    ip_address VARCHAR(45) NULLABLE,
    user_agent TEXT NULLABLE,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX user_id (user_id),
    INDEX last_activity (last_activity)
);
```

---

## 🔐 Authentication System

### Authentication Flow

```
┌─────────────────────────────────────────┐
│         User visits /login              │
│                                         │
│  AuthController::showLoginForm()        │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│    Enter credentials (email, password)  │
│                                         │
│   POST /login -> AuthController::login()│
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│   Validate input & Find staff by email  │
│                                         │
│   Staff::where('staff_email', $email)   │
└────────────┬────────────────────────────┘
             │
             ├─ Not Found? ────► Return to login with error
             │
             ▼
┌─────────────────────────────────────────┐
│  Verify password with Hash::check()     │
│                                         │
│  Hash::check($input, $hashed_password)  │
└────────────┬────────────────────────────┘
             │
             ├─ Incorrect? ────► Return to login with error
             │
             ▼
┌─────────────────────────────────────────┐
│  Create session with staff information  │
│                                         │
│  session()->put('staff_id', $id)        │
│  session()->put('staff_name', $name)    │
│  session()->put('staff_email', $email)  │
│  session()->regenerate()                │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│   Redirect to /staff_dashboard          │
│   Return success message                │
└─────────────────────────────────────────┘
```

### Session Variables

#### **Staff Session:**
```php
session('staff_id')      // Unique staff identifier
session('staff_name')    // Staff full name
session('staff_email')   // Staff email address
```

#### **Admin Session:**
```php
session('admin_id')      // Unique admin identifier
session('admin_name')    // Admin full name
session('admin_email')   // Admin email address
```

### Password Security

- **Algorithm:** bcrypt (BCRYPT_ROUNDS=12)
- **Hashing:** `Hash::make($password)` for storing
- **Verification:** `Hash::check($input, $hashed)` for login
- **Never stored in plaintext**

---

## 🚀 Installation & Setup

### Prerequisites
- Docker & Docker Compose installed
- PHP 8.4+
- Composer
- MySQL 8.0

### Step 1: Clone & Navigate
```bash
cd c:\Users\syami\Desktop\StaffAttendance_System\staff_attendance
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment
Copy `.env.example` to `.env` (already done):
```bash
cp .env.example .env
```

**Key .env variables:**
```dotenv
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=staffAttend_data
DB_USERNAME=root
DB_PASSWORD=root
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### Step 4: Start Docker Containers
```bash
cd c:\Users\syami\Desktop\StaffAttendance_System
docker-compose up -d
```

**This starts:**
- MySQL 8.0 on port 3307
- phpMyAdmin on port 8081

### Step 5: Generate Application Key
```bash
php artisan key:generate
```

### Step 6: Run Migrations
```bash
php artisan migrate:refresh --seed --force
```

**This will:**
- Drop all existing tables
- Create fresh database schema
- Seed test data with hashed passwords

### Step 7: Start Laravel Development Server
```bash
php artisan serve
```

Application is now available at: **http://localhost:8000**

---

## 📡 API Routes & Endpoints

### Public Routes (No Authentication Required)

#### **Staff Login**
```
GET    /login              → Show staff login form
POST   /login              → Process staff login
```

#### **Admin Login**
```
GET    /admin_login        → Show admin login form
POST   /admin_login        → Process admin login
```

#### **Debug/Testing**
```
GET    /test-db            → Return JSON with staff count and password status
```

---

### Protected Routes - Staff (Requires StaffAuth Middleware)

#### **Dashboard**
```
GET    /staff_dashboard    → Staff main dashboard
Route::name('staff.dashboard')
```

#### **Attendance**
```
GET    /attendance                  → Show attendance page with check-in/out buttons
POST   /attendance/check-in         → Record check-in time
POST   /attendance/check-out        → Record check-out time
Route names:
  - attendance.show
  - attendance.checkIn
  - attendance.checkOut
```

#### **Profile**
```
GET    /staff_profile              → Show staff profile
POST   /staff_profile/update       → Update staff profile
Route names:
  - staff.profile
  - staff.profile.update
```

#### **Logout**
```
GET    /staff_logout               → Clear session and logout
Route::name('staff.logout')
```

---

### Protected Routes - Admin (Requires AdminAuth Middleware)

#### **Dashboard**
```
GET    /admin_dashboard            → Admin main dashboard
Route::name('admin.dashboard')
```

#### **Attendance Management**
```
GET    /admin/attendance           → List all staff attendance for selected date
POST   /admin/attendance/mark      → Mark/update attendance for staff
Route names:
  - admin.attendance
  - admin.attendance.mark
```

#### **Attendance Reports**
```
GET    /admin/attendance/report    → Generate attendance reports with filters
Route::name('admin.attendance.report')

Query Parameters:
  - start_date: YYYY-MM-DD (default: first day of month)
  - end_date: YYYY-MM-DD (default: today)
  - staff_id: Staff ID (optional, filter by staff)
```

#### **Logout**
```
GET    /admin/logout               → Clear session and logout
Route::name('admin.logout')
```

---

## 👥 User Roles & Permissions

### Staff Role
**Can Access:**
- ✅ Own dashboard
- ✅ Check-in/check-out
- ✅ View own attendance history (30 days)
- ✅ View/edit own profile
- ✅ Logout

**Cannot Access:**
- ❌ Admin dashboard
- ❌ Other staff information
- ❌ Manage other attendance records
- ❌ View reports

---

### Admin Role
**Can Access:**
- ✅ Admin dashboard
- ✅ View all staff attendance
- ✅ Mark/edit attendance for any staff
- ✅ Filter attendance by date
- ✅ Generate attendance reports
- ✅ Filter reports by date range and staff
- ✅ View attendance statistics
- ✅ Logout

**Cannot Access:**
- ❌ Staff dashboard
- ❌ Staff profile pages

---

## 📁 File Structure Guide

### Controllers Breakdown

#### `AuthController.php`
- **Purpose:** Handle staff authentication
- **Methods:**
  - `showLoginForm()` - Display login page
  - `login()` - Process login credentials
  - `logout()` - Clear session and redirect

#### `AdminController.php`
- **Purpose:** Handle admin authentication and dashboard
- **Methods:**
  - `showLoginForm()` - Display admin login page
  - `login()` - Process admin login
  - `dashboard()` - Show admin dashboard
  - `logout()` - Clear session and redirect

#### `AttendanceController.php`
- **Purpose:** Handle staff attendance tracking
- **Methods:**
  - `show()` - Display attendance page with today's status and history
  - `checkIn()` - Record check-in time (creates attendance record)
  - `checkOut()` - Record check-out time (updates existing record)

#### `AdminAttendanceController.php`
- **Purpose:** Handle admin attendance management
- **Methods:**
  - `index()` - Show all staff attendance for selected date with stats
  - `mark()` - Mark/update attendance for specific staff
  - `report()` - Generate attendance reports with filters

#### `StaffProfileController.php`
- **Purpose:** Manage staff profile information
- **Methods:**
  - `show()` - Display staff profile
  - `update()` - Update profile with file upload support

#### `StaffController.php`
- **Purpose:** Handle staff dashboard
- **Methods:**
  - `dashboard()` - Show staff main dashboard
  - `logout()` - Logout functionality

---

### Models Breakdown

#### `Staff.php`
```php
protected $table = 'staff';
protected $primaryKey = 'staff_id';
public $timestamps = false;

protected $fillable = ['staff_name', 'staff_email', 'staff_password', 'team_id', 'created_at'];

// Relationships
public function profile() → hasOne(StaffProfile)
public function attendance() → hasMany(Attendance)
```

#### `Admin.php`
```php
protected $table = 'admin';
protected $primaryKey = 'admin_id';
public $timestamps = false;

protected $fillable = ['admin_name', 'admin_email', 'admin_password'];
```

#### `Attendance.php`
```php
protected $fillable = ['staff_id', 'attendance_date', 'check_in_time', 'check_out_time', 'status', 'remarks'];

// Relationships
public function staff() → belongsTo(Staff)
```

#### `StaffProfile.php`
```php
protected $fillable = ['staff_id', 'full_name', 'email', 'phone_number', 'address', 'position', 'department', 'profile_image'];

// Relationships
public function staff() → belongsTo(Staff)
```

---

### Middleware Breakdown

#### `StaffAuth.php`
- **Purpose:** Protect staff routes
- **Logic:** Checks if `session('staff_id')` exists
- **Action:** Redirects to `/login` if not authenticated

#### `AdminAuth.php`
- **Purpose:** Protect admin routes
- **Logic:** Checks if `session('admin_id')` exists
- **Action:** Redirects to `/admin_login` if not authenticated

#### **Middleware Registration** (Kernel.php)
```php
protected $middlewareAliases = [
    'staff.auth' => \App\Http\Middleware\StaffAuth::class,
    'admin.auth' => \App\Http\Middleware\AdminAuth::class,
];
```

---

## ⚙️ Configuration Files

### `.env` Configuration
```dotenv
# Application
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=staffAttend_data
DB_USERNAME=root
DB_PASSWORD=root

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true

# Other
BCRYPT_ROUNDS=12
QUEUE_CONNECTION=database
CACHE_STORE=file
```

### `docker-compose.yml`
- **MySQL:** Port 3307 (internal 3306)
- **phpMyAdmin:** Port 8081
- **Volume:** `mysql_data/` (persistent storage)

---

## 🧪 Test Credentials

After running `php artisan migrate:refresh --seed --force`:

### Staff Accounts
| Email | Password | Name |
|-------|----------|------|
| ahmad@utm.edu.my | password123 | Ahmad Hassan |
| siti@utm.edu.my | password123 | Siti Nurhaliza |
| test@utm.edu.my | password123 | Test User |

### Admin Account
| Email | Password | Name |
|-------|----------|------|
| admin@utm.edu.my | admin123 | Admin User |

---

## 🐛 Troubleshooting

### Issue 1: "SQLSTATE[HY000]: General error: 1030 Got error"
**Cause:** Database tables don't exist
**Solution:**
```bash
php artisan migrate:refresh --seed --force
```

### Issue 2: "Target class [admin.auth] does not exist"
**Cause:** Middleware not registered or session table missing
**Solution:**
1. Check `app/Http/Kernel.php` has middleware aliases
2. Run migrations: `php artisan migrate --force`
3. Restart server: `php artisan serve`

### Issue 3: "Illuminate\Database\QueryException - table doesn't exist"
**Cause:** Specific table not created
**Solution:**
1. Check migration files in `database/migrations/`
2. Run: `php artisan migrate --force`
3. Verify with: `php artisan migrate:status`

### Issue 4: Login page but toggle icon not showing
**Cause:** Font Awesome CSS not loaded
**Solution:** Check HTML head has:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

### Issue 5: Session data lost after redirect
**Cause:** SESSION_DRIVER set to 'file' instead of 'database'
**Solution:** In `.env`:
```dotenv
SESSION_DRIVER=database
```
Then run migrations to create sessions table.

### Issue 6: Cannot access admin dashboard after login
**Cause:** AdminAuth middleware disabled or session not set correctly
**Solution:**
1. Check `app/Http/Middleware/AdminAuth.php` is enabled
2. Verify AdminController::login() sets `session('admin_id')`
3. Run: `php artisan cache:clear && php artisan config:clear`

### Issue 7: Database connection failed
**Cause:** MySQL container not running or port mismapped
**Solution:**
```bash
# Check containers
docker-compose ps

# Restart containers
docker-compose restart

# Check MySQL logs
docker-compose logs mysql
```

### Issue 8: "Undefined array key" in views
**Cause:** Variable not passed from controller
**Solution:**
1. Check controller passes variable: `view('name', compact('variable'))`
2. Check view uses correct variable name: `{{ $variable }}`

---

## 🔄 Workflow Examples

### Complete Staff Login Workflow
```
1. Visit http://localhost:8000/login
2. Enter: test@utm.edu.my / password123
3. System validates credentials
4. Session created with staff_id, staff_name, staff_email
5. Redirected to /staff_dashboard
6. Can now access: attendance, profile, logout
```

### Complete Check-in/Out Workflow
```
1. Staff visits /attendance page
2. Clicks "Check-in" button
3. POST request to /attendance/check-in
4. AttendanceController::checkIn() creates record with current time
5. Attendance record saved with check_in_time
6. Staff can later click "Check-out"
7. POST request to /attendance/check-out
8. Updates same attendance record with check_out_time
9. Status auto-updates based on check_in_time (present/late/absent/leave)
```

### Complete Admin Report Workflow
```
1. Admin visits /admin/attendance/report
2. Selects start_date and end_date
3. Optionally filters by staff_id
4. System queries attendance records in date range
5. Calculates summary: total, present, absent, late, leave
6. Displays attendance report in table format
7. Can export or print report
```

---

## 📞 Support & Debugging

### Enable Debug Mode
Edit `.env`:
```dotenv
APP_DEBUG=true
LOG_LEVEL=debug
```

### View Application Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

### List All Routes
```bash
php artisan route:list
```

### Database Inspection
```bash
# Access phpMyAdmin
Visit http://localhost:8081
Username: root
Password: root
Database: staffAttend_data
```

### Test Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPDO()
>>> Staff::all()
>>> Admin::all()
```

---

## 📝 Summary of Fixes Applied

### 1. **Staff Model - primaryKey Typo**
- **Before:** `protected $primarykey = 'staff_id'` (lowercase)
- **After:** `protected $primaryKey = 'staff_id'` (correct camelCase)
- **Impact:** Fixes ORM queries for Staff model

### 2. **Added Staff Model Relationships**
- **Added:** `profile()` → hasOne(StaffProfile)
- **Added:** `attendance()` → hasMany(Attendance)
- **Impact:** Enables eager loading: `Staff::with('profile', 'attendance')->get()`

### 3. **StaffAuth Middleware - Enabled & Fixed**
- **Before:** Disabled, checked for `staff_email` (inconsistent)
- **After:** Enabled, checks for `staff_id` (correct)
- **Impact:** Routes now properly protected, staff must login to access dashboard

### 4. **AdminAuth Middleware - Enabled**
- **Before:** Disabled for debugging
- **After:** Enabled, checks for `admin_id`
- **Impact:** Admin routes now properly protected

### 5. **Font Awesome CSS - Added to Admin Login**
- **Before:** Missing `<link>` tag for Font Awesome
- **After:** Added CDN link in head
- **Impact:** Password toggle eye icon now displays correctly

### 6. **Sessions Table Migration - Created**
- **File:** `2025_01_01_000000_create_sessions_table.php`
- **Impact:** Database-driven session storage now functional

---

## ✅ Verification Checklist

After applying all fixes, verify:

- [ ] Database migrations completed without errors
- [ ] Test credentials work for both staff and admin
- [ ] Staff can login and see dashboard
- [ ] Staff can check-in/check-out
- [ ] Staff profile shows and can be edited
- [ ] Admin can login and see admin dashboard
- [ ] Admin can view attendance page
- [ ] Admin can mark attendance for staff
- [ ] Admin can generate reports with filters
- [ ] Logout clears session properly
- [ ] Navigation links work correctly
- [ ] Password toggle works in login forms
- [ ] Middleware protects routes (try accessing without login)
- [ ] Error messages display properly
- [ ] No console errors in browser (F12)

---

**Last Updated:** November 20, 2025  
**Laravel Version:** 12.37.0  
**PHP Version:** 8.4.14  
**MySQL Version:** 8.0
