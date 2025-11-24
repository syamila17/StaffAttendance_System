# Staff Attendance System - Enhanced Database Schema Documentation

## 📋 Table of Contents
1. [Overview](#overview)
2. [Database Design Philosophy](#database-design-philosophy)
3. [Complete Database Schema](#complete-database-schema)
4. [Table Definitions](#table-definitions)
5. [Relationships & Foreign Keys](#relationships--foreign-keys)
6. [Query Examples](#query-examples)
7. [Implementation Guide](#implementation-guide)
8. [Best Practices](#best-practices)

---

## 🎯 Overview

Your Staff Attendance System now supports a complete organizational hierarchy:

```
ORGANIZATION
    ├── DEPARTMENT (IT, HR, Operations)
    │   ├── TEAM (Backend, Frontend, Recruitment)
    │   │   ├── STAFF (Individual employees)
    │   │   │   └── ATTENDANCE (Check-in/out records)
    │   │   └── REPORT (Department/Team/Staff reports)
    │   │       └── REPORT DETAIL (Individual records in report)
    │   └── MANAGER (Staff acting as department manager)
    └── ADMIN (System administrators)
```

---

## 🏗️ Database Design Philosophy

### Why These Tables?

**✅ SHOULD ADD: YES - Here's Why**

1. **Departments & Teams Tables**
   - ✅ Better organization & structure
   - ✅ Hierarchical reporting (department → team → staff)
   - ✅ Easy filtering by department/team
   - ✅ Support for department-level reports
   - ✅ Manage multiple teams per department
   - ✅ Scale as organization grows

2. **Attendance Reports Tables**
   - ✅ Store generated reports (don't lose data)
   - ✅ Quick historical report access
   - ✅ Track who generated reports and when
   - ✅ Support multiple report types
   - ✅ Calculate statistics efficiently
   - ✅ Audit trail for compliance

### Design Principles Applied

| Principle | Implementation |
|-----------|-----------------|
| **Normalization** | Separate tables for departments, teams, reports |
| **Referential Integrity** | Foreign keys with cascade/null deletion |
| **Scalability** | Support for organizational growth |
| **Flexibility** | Optional fields for future expansion |
| **Performance** | Indexes on frequently queried columns |
| **Auditability** | Track created_at, updated_at, generated_at |

---

## 🗄️ Complete Database Schema

### Visual Representation

```
┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│                    ADMIN (Administrators)                          │
│                   - admin_id (PK)                                  │
│                   - admin_email (UNIQUE)                           │
│                   - admin_password (hashed)                        │
│                   - timestamps                                     │
│                                                                     │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             │ generates
                             ▼
┌────────────────────────────────────────────────────────────────────┐
│          ATTENDANCE_REPORTS (Generated Reports)                    │
│          - report_id (PK)                                          │
│          - admin_id (FK)                                           │
│          - report_type: department/team/staff/summary              │
│          - start_date, end_date                                    │
│          - attendance statistics                                   │
│          - timestamps                                              │
└────────┬──────────────────────────────────────────────────────────┘
         │ contains
         ▼
┌────────────────────────────────────────────────────────────────────┐
│     ATTENDANCE_REPORT_DETAILS (Individual records in report)       │
│     - detail_id (PK)                                               │
│     - report_id (FK) → ATTENDANCE_REPORTS                          │
│     - staff_id (FK) → STAFF                                        │
│     - attendance_date                                              │
│     - check_in/out times                                           │
│     - status, duration, remarks                                    │
└────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────┐
│                    DEPARTMENTS                                       │
│                   - department_id (PK)                              │
│                   - department_name (UNIQUE)                        │
│                   - department_code (UNIQUE)                        │
│                   - location                                        │
│                   - manager_id (FK → STAFF)                         │
│                   - status (active/inactive)                        │
│                   - timestamps                                      │
└──────────────┬───────────────────────────────────────────────────────┘
               │ contains many
               ▼
┌──────────────────────────────────────────────────────────────────────┐
│                      TEAMS                                           │
│                   - team_id (PK)                                    │
│                   - team_name                                       │
│                   - team_code (UNIQUE)                              │
│                   - department_id (FK)                              │
│                   - team_lead_id (FK → STAFF)                       │
│                   - status (active/inactive)                        │
│                   - UNIQUE(team_name, department_id)                │
│                   - timestamps                                      │
└──────────────┬───────────────────────────────────────────────────────┘
               │ contains many
               ▼
┌──────────────────────────────────────────────────────────────────────┐
│                       STAFF                                          │
│                   - staff_id (PK)                                   │
│                   - staff_name                                      │
│                   - staff_email (UNIQUE)                            │
│                   - staff_password (hashed)                         │
│                   - department_id (FK)                              │
│                   - team_id (FK)                                    │
│                   - timestamps                                      │
└──────────────┬───────────────────────────────────────────────────────┘
               │ has
               ▼
┌──────────────────────────────────────────────────────────────────────┐
│       STAFF_PROFILE (Extended profile information)                   │
│       - id (PK)                                                      │
│       - staff_id (FK, UNIQUE)                                        │
│       - full_name, phone_number, address                             │
│       - position, department, profile_image                          │
│       - timestamps                                                   │
└──────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────┐
│       ATTENDANCE (Daily check-in/out records)                        │
│       - id (PK)                                                      │
│       - staff_id (FK)                                                │
│       - attendance_date                                              │
│       - check_in_time, check_out_time                                │
│       - status: present/absent/late/leave                            │
│       - remarks, timestamps                                          │
│       - UNIQUE(staff_id, attendance_date)                            │
└──────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────┐
│       SESSIONS (Database-driven session storage)                     │
│       - id (PK)                                                      │
│       - user_id, ip_address, user_agent                              │
│       - payload (session data), last_activity                        │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 📊 Table Definitions

### 1. DEPARTMENTS Table

```sql
CREATE TABLE departments (
    department_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(255) UNIQUE NOT NULL,
    department_code VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULLABLE,
    location VARCHAR(255) NULLABLE,
    manager_id BIGINT UNSIGNED NULLABLE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (manager_id) REFERENCES staff(staff_id) ON DELETE SET NULL
);
```

**Columns:**
- `department_id` - Unique identifier (auto-increment)
- `department_name` - Department name (e.g., "Information Technology")
- `department_code` - Short code (e.g., "IT")
- `description` - Optional description of department's purpose
- `location` - Physical location of department
- `manager_id` - FK to staff table (who manages this department)
- `status` - active/inactive for soft deletion
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- PRIMARY KEY on `department_id`
- UNIQUE on `department_name`
- UNIQUE on `department_code`
- INDEX on `manager_id`

---

### 2. TEAMS Table

```sql
CREATE TABLE teams (
    team_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    team_name VARCHAR(255) NOT NULL,
    team_code VARCHAR(255) UNIQUE NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    team_lead_id BIGINT UNSIGNED NULLABLE,
    description TEXT NULLABLE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE,
    FOREIGN KEY (team_lead_id) REFERENCES staff(staff_id) ON DELETE SET NULL,
    UNIQUE KEY unique_team_per_dept (team_name, department_id)
);
```

**Columns:**
- `team_id` - Unique identifier
- `team_name` - Team name (e.g., "Backend Development")
- `team_code` - Unique code (e.g., "IT-BACKEND")
- `department_id` - FK to departments (which department)
- `team_lead_id` - FK to staff (team lead/manager)
- `description` - Team description
- `status` - active/inactive
- `created_at`, `updated_at` - Timestamps

**Constraints:**
- Same team name can exist in different departments
- But UNIQUE constraint on (team_name, department_id) ensures uniqueness per department

**Indexes:**
- PRIMARY KEY on `team_id`
- UNIQUE on `team_code`
- FOREIGN KEY on `department_id`
- FOREIGN KEY on `team_lead_id`
- UNIQUE on `(team_name, department_id)`

---

### 3. STAFF Table (Updated)

```sql
ALTER TABLE staff ADD COLUMN (
    department_id BIGINT UNSIGNED NULLABLE AFTER team_id,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
);
```

**New Columns:**
- `department_id` - FK to departments (which department staff belongs to)

**Updated Foreign Keys:**
- `team_id` → teams table (with ON DELETE SET NULL)
- `department_id` → departments table (with ON DELETE SET NULL)

---

### 4. ATTENDANCE_REPORTS Table

```sql
CREATE TABLE attendance_reports (
    report_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    admin_id BIGINT UNSIGNED NOT NULL,
    report_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    report_type ENUM('department', 'team', 'staff', 'summary') DEFAULT 'summary',
    department_id BIGINT UNSIGNED NULLABLE,
    team_id BIGINT UNSIGNED NULLABLE,
    staff_id BIGINT UNSIGNED NULLABLE,
    
    total_days INT DEFAULT 0,
    present_days INT DEFAULT 0,
    absent_days INT DEFAULT 0,
    late_days INT DEFAULT 0,
    leave_days INT DEFAULT 0,
    attendance_percentage DECIMAL(5, 2) DEFAULT 0,
    
    remarks TEXT NULLABLE,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE SET NULL,
    
    INDEX idx_report_type (report_type),
    INDEX idx_date_range (start_date, end_date)
);
```

**Columns:**
- `report_id` - Unique report identifier
- `admin_id` - Who generated the report
- `report_name` - User-friendly report name
- `start_date` - Report period start
- `end_date` - Report period end
- `report_type` - Type: department/team/staff/summary
- `department_id` - If report_type is 'department'
- `team_id` - If report_type is 'team'
- `staff_id` - If report_type is 'staff'
- `total_days` - Total working days in period
- `present_days` - Days marked as present
- `absent_days` - Days marked as absent
- `late_days` - Days marked as late
- `leave_days` - Days marked as leave
- `attendance_percentage` - Calculated: (present_days / total_days) * 100
- `remarks` - Optional notes about report
- `generated_at` - When report was created
- `created_at`, `updated_at` - Timestamps

**Report Types:**
```
- 'department' → Reports for entire department
- 'team'       → Reports for specific team
- 'staff'      → Reports for individual staff
- 'summary'    → Organization-wide summary
```

---

### 5. ATTENDANCE_REPORT_DETAILS Table

```sql
CREATE TABLE attendance_report_details (
    detail_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    attendance_id BIGINT UNSIGNED NULLABLE,
    
    attendance_date DATE NOT NULL,
    check_in_time TIME NULLABLE,
    check_out_time TIME NULLABLE,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'absent',
    duration_minutes INT NULLABLE,
    remarks TEXT NULLABLE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (report_id) REFERENCES attendance_reports(report_id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    FOREIGN KEY (attendance_id) REFERENCES attendance(id) ON DELETE SET NULL,
    
    INDEX idx_report (report_id),
    INDEX idx_staff (staff_id),
    INDEX idx_date (attendance_date)
);
```

**Columns:**
- `detail_id` - Unique detail identifier
- `report_id` - FK to parent report (CASCADE delete)
- `staff_id` - FK to staff member
- `attendance_id` - FK to original attendance record (optional, for reference)
- `attendance_date` - Date of this attendance record
- `check_in_time`, `check_out_time` - Times from original record
- `status` - Copied from original or overridden
- `duration_minutes` - Work hours in minutes
- `remarks` - Optional notes
- `created_at`, `updated_at` - Timestamps

**Purpose:**
- Store snapshot of attendance for historical report data
- When attendance record changes, report detail remains unchanged
- Allows historical comparison and audit trail

---

## 🔗 Relationships & Foreign Keys

### Organization Hierarchy

```
Department (1) ──────────── (N) Team
    ↓
    │─── manager_id ───→ Staff (as manager)
    │
Team (1) ──────────── (N) Staff
    ↓
    │─── team_lead_id ───→ Staff (as team lead)
    │
Staff (1) ──────────── (N) Attendance
         ──────────── (N) StaffProfile
         ──────────── (N) AttendanceReport (as subject)
         ──────────── (N) AttendanceReportDetail
```

### Report Relationships

```
Admin (1) ──────────── (N) AttendanceReport
    ↓
AttendanceReport (1) ──────────── (N) AttendanceReportDetail
    ├─→ Department (optional)
    ├─→ Team (optional)
    └─→ Staff (optional)
```

### Cascade Rules

| Relationship | Delete Rule | Reason |
|-------------|-------------|--------|
| Department → Staff | SET NULL | Staff shouldn't be deleted when dept deletes |
| Team → Staff | SET NULL | Staff shouldn't be deleted when team deletes |
| Staff → Team Lead | SET NULL | Team continues without lead if staff deleted |
| Admin → Report | CASCADE | Delete reports when admin deleted |
| Report → Details | CASCADE | Delete details when report deleted |
| Staff → Attendance | CASCADE | Delete attendance when staff deleted |

---

## 💾 Complete Model Relationships

### Staff Model

```php
class Staff extends Model {
    // One-to-one
    public function profile() → hasOne(StaffProfile)
    
    // One-to-many
    public function attendance() → hasMany(Attendance)
    public function teamsManaged() → hasMany(Team, 'team_lead_id')
    public function departmentsManaged() → hasMany(Department, 'manager_id')
    public function reports() → hasMany(AttendanceReport, 'staff_id')
    
    // Belongs to
    public function department() → belongsTo(Department)
    public function team() → belongsTo(Team)
}
```

### Department Model

```php
class Department extends Model {
    // One-to-many
    public function teams() → hasMany(Team)
    public function staff() → hasMany(Staff)
    public function reports() → hasMany(AttendanceReport)
    
    // Belongs to (Staff as manager)
    public function manager() → belongsTo(Staff, 'manager_id')
}
```

### Team Model

```php
class Team extends Model {
    // One-to-many
    public function staff() → hasMany(Staff)
    public function reports() → hasMany(AttendanceReport)
    
    // Belongs to
    public function department() → belongsTo(Department)
    public function teamLead() → belongsTo(Staff, 'team_lead_id')
}
```

### AttendanceReport Model

```php
class AttendanceReport extends Model {
    // Belongs to
    public function admin() → belongsTo(Admin)
    public function department() → belongsTo(Department)
    public function team() → belongsTo(Team)
    public function staffMember() → belongsTo(Staff, 'staff_id')
    
    // One-to-many
    public function details() → hasMany(AttendanceReportDetail)
}
```

### AttendanceReportDetail Model

```php
class AttendanceReportDetail extends Model {
    // Belongs to
    public function report() → belongsTo(AttendanceReport)
    public function staff() → belongsTo(Staff)
    public function attendance() → belongsTo(Attendance)
}
```

---

## 📝 Query Examples

### 1. Get All Staff in IT Department

```php
$itStaff = Department::where('department_code', 'IT')
    ->with('staff')
    ->first()
    ->staff;
```

### 2. Get All Teams in HR Department

```php
$hrTeams = Department::where('department_name', 'Human Resources')
    ->with('teams')
    ->first()
    ->teams;
```

### 3. Get Backend Team Members

```php
$backendTeam = Team::where('team_code', 'IT-BACKEND')
    ->with('staff')
    ->first();

$members = $backendTeam->staff;
```

### 4. Get Department Manager

```php
$manager = Department::find($deptId)->manager;
// or
$manager = Staff::find($deptManagerId);
```

### 5. Get Staff's Attendance for Date Range

```php
$attendance = Attendance::where('staff_id', $staffId)
    ->whereBetween('attendance_date', [$startDate, $endDate])
    ->get();
```

### 6. Get Reports Generated Today

```php
$todayReports = AttendanceReport::whereDate('generated_at', today())
    ->with('admin', 'department', 'team', 'staffMember')
    ->get();
```

### 7. Get Department Report Details

```php
$report = AttendanceReport::find($reportId);
$details = $report->details()->with('staff')->get();

foreach ($details as $detail) {
    echo $detail->staff->staff_name . ": " . $detail->status;
}
```

### 8. Calculate Department Attendance Percentage

```php
$dept = Department::find($deptId);
$staff = $dept->staff;

$totalDays = 0;
$presentDays = 0;

foreach ($staff as $member) {
    $attendance = Attendance::where('staff_id', $member->staff_id)
        ->whereBetween('attendance_date', [$start, $end])
        ->get();
    
    $totalDays += $attendance->count();
    $presentDays += $attendance->where('status', 'present')->count();
}

$percentage = ($presentDays / $totalDays) * 100;
```

### 9. Get Team Lead's Team Members

```php
$teamLead = Staff::find($staffId);
$managedTeams = $teamLead->teamsManaged()->with('staff')->get();
```

### 10. Generate Department Report

```php
$report = AttendanceReport::create([
    'admin_id' => Auth::id(),
    'report_name' => 'IT Department Report - Nov 2025',
    'start_date' => '2025-11-01',
    'end_date' => '2025-11-30',
    'report_type' => 'department',
    'department_id' => $deptId,
    'total_days' => 22,
    'present_days' => 20,
    'absent_days' => 2,
    'attendance_percentage' => 90.91,
]);

// Add details for each day
foreach ($attendanceRecords as $record) {
    AttendanceReportDetail::create([
        'report_id' => $report->report_id,
        'staff_id' => $record->staff_id,
        'attendance_id' => $record->id,
        'attendance_date' => $record->attendance_date,
        'check_in_time' => $record->check_in_time,
        'check_out_time' => $record->check_out_time,
        'status' => $record->status,
    ]);
}
```

---

## 🚀 Implementation Guide

### Step 1: Create Migration Files

All migration files have been created:
- `2025_11_20_000001_create_departments_table.php`
- `2025_11_20_000002_create_teams_table.php`
- `2025_11_20_000003_add_department_to_staff.php`
- `2025_11_20_000004_create_attendance_reports_table.php`
- `2025_11_20_000005_create_attendance_report_details_table.php`

### Step 2: Run Migrations

```bash
# This will create/update all tables
php artisan migrate:refresh --seed --force
```

### Step 3: Test Database

```bash
php artisan tinker

# Test departments
>>> $dept = Department::first();
>>> $dept->teams;
>>> $dept->staff;

# Test teams
>>> $team = Team::first();
>>> $team->staff;
>>> $team->department;

# Test relationships
>>> $staff = Staff::first();
>>> $staff->department;
>>> $staff->team;
>>> $staff->teamsManaged;
```

### Step 4: Create Controllers

For reports management:

```bash
php artisan make:controller ReportController
php artisan make:controller DepartmentController
php artisan make:controller TeamController
```

### Step 5: Create Routes

```php
Route::middleware(['admin.auth'])->group(function () {
    // Existing routes...
    
    // Report management
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/admin/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/admin/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    
    // Department management
    Route::get('/admin/departments', [DepartmentController::class, 'index']);
    Route::get('/admin/teams', [TeamController::class, 'index']);
});
```

### Step 6: Create Views

- `resources/views/admin/reports/index.blade.php` - List reports
- `resources/views/admin/reports/generate.blade.php` - Generate report form
- `resources/views/admin/reports/show.blade.php` - View report details

---

## ✅ Best Practices

### 1. Always Use Relationships

❌ **Bad:**
```php
$staff = Staff::find($id);
$team = Team::find($staff->team_id);
$dept = Department::find($team->department_id);
```

✅ **Good:**
```php
$staff = Staff::with('team.department')->find($id);
$dept = $staff->team->department;
```

### 2. Use Eager Loading

❌ **Bad (N+1 Problem):**
```php
$staff = Staff::all();
foreach ($staff as $member) {
    echo $member->team->team_name; // Extra query per staff!
}
```

✅ **Good:**
```php
$staff = Staff::with('team')->get();
foreach ($staff as $member) {
    echo $member->team->team_name; // Only 2 queries total
}
```

### 3. Use Query Scopes

✅ **Create scopes in model:**
```php
class AttendanceReport extends Model {
    public function scopeByDepartment($query, $deptId) {
        return $query->where('department_id', $deptId);
    }
    
    public function scopeByDateRange($query, $start, $end) {
        return $query->whereBetween('start_date', [$start, $end]);
    }
}
```

✅ **Use in controller:**
```php
$reports = AttendanceReport::byDepartment($deptId)
    ->byDateRange($start, $end)
    ->get();
```

### 4. Validate Foreign Keys

```php
$validated = $request->validate([
    'staff_id' => 'required|exists:staff,staff_id',
    'department_id' => 'required|exists:departments,department_id',
    'team_id' => 'required|exists:teams,team_id',
]);
```

### 5. Use Transactions for Multi-Table Updates

```php
DB::transaction(function () {
    $dept = Department::create($deptData);
    $team = Team::create([..., 'department_id' => $dept->id]);
    Staff::update(['department_id' => $dept->id]);
});
```

### 6. Index for Performance

Already implemented:
- `INDEX` on foreign keys
- `UNIQUE` on codes
- `INDEX` on status fields
- `INDEX` on date ranges

### 7. Use Soft Deletes (Optional Enhancement)

For data preservation:
```php
use SoftDeletes;

protected $dates = ['deleted_at'];
```

### 8. Create Database Views (Optional)

For complex reporting:
```sql
CREATE VIEW department_attendance_summary AS
SELECT 
    d.department_id,
    d.department_name,
    COUNT(DISTINCT s.staff_id) as total_staff,
    COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_count,
    COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_count
FROM departments d
LEFT JOIN staff s ON d.department_id = s.department_id
LEFT JOIN attendance a ON s.staff_id = a.staff_id
GROUP BY d.department_id, d.department_name;
```

---

## 📈 Scaling Considerations

### As Organization Grows

1. **Sub-departments** - Add `parent_department_id` for nested structure
2. **Job Titles/Positions** - Create positions table for staff roles
3. **Shift Scheduling** - Add shifts table for different work schedules
4. **Leave Management** - Create leave_requests and leave_balances tables
5. **Performance Metrics** - Track KPIs and performance indicators
6. **Integration** - API endpoints for external systems

### Performance Optimization

- Archive old attendance records to separate table
- Create materialized views for reports
- Add caching layer for frequently accessed data
- Partition large tables by date

---

## 📚 Summary

| Aspect | Details |
|--------|---------|
| **New Tables** | departments, teams, attendance_reports, attendance_report_details |
| **New Models** | Department, Team, AttendanceReport, AttendanceReportDetail |
| **Updated Table** | staff (added department_id, foreign keys) |
| **New Relationships** | 15+ new relationships implemented |
| **Report Types** | department, team, staff, summary |
| **Best For** | Organizations with multiple departments and teams |
| **Scalability** | Excellent - supports hierarchical organization |
| **Flexibility** | High - allows various reporting scenarios |
| **Performance** | Optimized with indexes and relationships |

---

**Version:** 2.0  
**Date:** November 20, 2025  
**Status:** ✅ Ready to Deploy
