# Implementation Steps - Department, Team & Reports System

## 🚀 Quick Start (Complete Setup)

### Prerequisites
- Project already running with Laravel 12
- Docker MySQL container running
- Database: staffAttend_data

---

## 📋 Step-by-Step Implementation

### Step 1: Review New Migrations

**Files Created:**
```
database/migrations/
  ├── 2025_11_20_000001_create_departments_table.php
  ├── 2025_11_20_000002_create_teams_table.php
  ├── 2025_11_20_000003_add_department_to_staff.php
  ├── 2025_11_20_000004_create_attendance_reports_table.php
  └── 2025_11_20_000005_create_attendance_report_details_table.php
```

**What They Do:**
1. Creates `departments` table
2. Creates `teams` table (with FK to departments)
3. Adds `department_id` column to `staff` table
4. Creates `attendance_reports` table
5. Creates `attendance_report_details` table

### Step 2: Review New Models

**Files Created:**
```
app/Models/
  ├── Department.php (new)
  ├── Team.php (new)
  ├── AttendanceReport.php (new)
  ├── AttendanceReportDetail.php (new)
  └── Staff.php (UPDATED - added relationships)
```

### Step 3: Run Database Migrations

```powershell
cd c:\Users\syami\Desktop\StaffAttendance_System\staff_attendance

# Refresh database and seed new data
php artisan migrate:refresh --seed --force
```

**This will:**
- Drop all existing tables
- Run all migrations in order (creating new tables)
- Seed data including:
  - 3 departments (IT, HR, Operations)
  - 5 teams (Backend, Frontend, Recruitment, Payroll, Support)
  - 3 staff members assigned to departments/teams
  - 1 admin user

### Step 4: Verify Database Setup

**Option A: Using phpMyAdmin**
```
URL: http://localhost:8081
Username: root
Password: root
Database: staffAttend_data

Check tables:
- departments (should have 3 rows)
- teams (should have 5 rows)
- staff (should have 3 rows, with department_id and team_id filled)
- attendance_reports (empty)
- attendance_report_details (empty)
```

**Option B: Using Laravel Tinker**
```powershell
php artisan tinker

# Test departments
>>> Department::all();
>>> Department::with('teams', 'staff')->first();

# Test teams
>>> Team::all();
>>> Team::with('department', 'staff')->first();

# Test staff relationships
>>> $staff = Staff::first();
>>> $staff->department;
>>> $staff->team;
>>> $staff->teamsManaged;
>>> $staff->departmentsManaged;
```

---

## 🎯 Organizational Structure Created

After seeding, your organization looks like:

```
ORGANIZATION
│
├── DEPARTMENT: Information Technology (IT)
│   │
│   ├── TEAM: Backend Development (IT-BACKEND)
│   │   ├── Ahmad Hassan (Team Lead)
│   │   └── Team Members (Backend Team)
│   │
│   ├── TEAM: Frontend Development (IT-FRONTEND)
│   │   ├── Siti Nurhaliza (Team Lead)
│   │   └── Team Members (Frontend Team)
│   │
│   └── Manager: Ahmad Hassan (IT Manager)
│
├── DEPARTMENT: Human Resources (HR)
│   │
│   ├── TEAM: Recruitment (HR-RECRUIT)
│   │   ├── Test User (Team Lead)
│   │   └── Team Members
│   │
│   ├── TEAM: Payroll (HR-PAYROLL)
│   │   └── Team Members
│   │
│   └── Manager: Test User (HR Manager)
│
└── DEPARTMENT: Operations (OPS)
    │
    └── TEAM: Customer Support (OPS-SUPPORT)
        └── Team Members
```

---

## 🔍 Test Data Structure

### Departments
| ID | Name | Code | Manager | Status |
|----|------|------|---------|--------|
| 1 | Information Technology | IT | Ahmad Hassan | active |
| 2 | Human Resources | HR | Test User | active |
| 3 | Operations | OPS | (null) | active |

### Teams
| ID | Name | Code | Department | Team Lead | Status |
|----|------|------|------------|-----------|--------|
| 1 | Backend Development | IT-BACKEND | IT | Ahmad Hassan | active |
| 2 | Frontend Development | IT-FRONTEND | IT | Siti Nurhaliza | active |
| 3 | Recruitment | HR-RECRUIT | HR | Test User | active |
| 4 | Payroll | HR-PAYROLL | HR | (null) | active |
| 5 | Customer Support | OPS-SUPPORT | OPS | (null) | active |

### Staff with Assignments
| ID | Name | Email | Department | Team |
|----|------|-------|------------|------|
| 1 | Ahmad Hassan | ahmad@utm.edu.my | IT | Backend |
| 2 | Siti Nurhaliza | siti@utm.edu.my | IT | Frontend |
| 3 | Test User | test@utm.edu.my | HR | Recruitment |

---

## 🎨 Next Steps - Create Admin Views

### 1. Create Department Management View

**File:** `resources/views/admin/departments/index.blade.php`

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Departments</h1>
    
    <button class="btn btn-primary">Add Department</button>
    
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Location</th>
                <th>Manager</th>
                <th>Teams</th>
                <th>Staff</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $dept)
            <tr>
                <td>{{ $dept->department_code }}</td>
                <td>{{ $dept->department_name }}</td>
                <td>{{ $dept->location }}</td>
                <td>{{ $dept->manager?->staff_name ?? 'N/A' }}</td>
                <td>{{ $dept->teams->count() }}</td>
                <td>{{ $dept->staff->count() }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-edit">Edit</a>
                    <a href="#" class="btn btn-sm btn-delete">Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

### 2. Create Team Management View

**File:** `resources/views/admin/teams/index.blade.php`

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Teams</h1>
    
    <button class="btn btn-primary">Add Team</button>
    
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Department</th>
                <th>Team Lead</th>
                <th>Staff Count</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teams as $team)
            <tr>
                <td>{{ $team->team_code }}</td>
                <td>{{ $team->team_name }}</td>
                <td>{{ $team->department->department_name }}</td>
                <td>{{ $team->teamLead?->staff_name ?? 'N/A' }}</td>
                <td>{{ $team->staff->count() }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-edit">Edit</a>
                    <a href="#" class="btn btn-sm btn-delete">Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

### 3. Create Report Generation View

**File:** `resources/views/admin/reports/generate.blade.php`

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generate Attendance Report</h1>
    
    <form method="POST" action="{{ route('reports.generate') }}">
        @csrf
        
        <div class="form-group">
            <label>Report Name</label>
            <input type="text" name="report_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Report Type</label>
            <select name="report_type" class="form-control" onchange="updateFilters()">
                <option value="summary">Organization Summary</option>
                <option value="department">Department Report</option>
                <option value="team">Team Report</option>
                <option value="staff">Staff Report</option>
            </select>
        </div>
        
        <div id="filters">
            <div class="form-group" id="dept-filter" style="display:none;">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    @foreach($departments as $dept)
                    <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" id="team-filter" style="display:none;">
                <label>Team</label>
                <select name="team_id" class="form-control">
                    @foreach($teams as $team)
                    <option value="{{ $team->team_id }}">
                        {{ $team->department->department_name }} - {{ $team->team_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" id="staff-filter" style="display:none;">
                <label>Staff Member</label>
                <select name="staff_id" class="form-control">
                    @foreach($staff as $member)
                    <option value="{{ $member->staff_id }}">
                        {{ $member->staff_name }} ({{ $member->department->department_name }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
</div>

<script>
function updateFilters() {
    const type = document.querySelector('select[name="report_type"]').value;
    document.getElementById('dept-filter').style.display = (type === 'department') ? 'block' : 'none';
    document.getElementById('team-filter').style.display = (type === 'team') ? 'block' : 'none';
    document.getElementById('staff-filter').style.display = (type === 'staff') ? 'block' : 'none';
}
</script>
@endsection
```

---

## 🛠 Create Controllers

### 1. DepartmentController

```bash
php artisan make:controller DepartmentController
```

### 2. TeamController

```bash
php artisan make:controller TeamController
```

### 3. ReportController

```bash
php artisan make:controller ReportController
```

---

## 📡 Add Routes

**File:** `routes/web.php`

```php
Route::middleware(['admin.auth'])->group(function () {
    // Existing routes...
    
    // Department management
    Route::get('/admin/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/admin/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/admin/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/admin/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    
    // Team management
    Route::get('/admin/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::post('/admin/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::put('/admin/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/admin/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy');
    
    // Report management
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/admin/reports/generate', [ReportController::class, 'generateForm'])->name('reports.generateForm');
    Route::post('/admin/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/admin/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
});
```

---

## ✅ Verification Checklist

After completing all steps:

- [ ] All migrations run successfully
- [ ] phpMyAdmin shows 5 new tables
- [ ] Departments table has 3 records
- [ ] Teams table has 5 records
- [ ] Staff table has department_id and team_id filled
- [ ] Models created: Department, Team, AttendanceReport, AttendanceReportDetail
- [ ] Staff model has new relationships
- [ ] Can query: `Department::with('teams', 'staff')->get()`
- [ ] Can query: `Team::with('department', 'staff')->get()`
- [ ] Test data seeded correctly
- [ ] No Laravel errors in log files

---

## 🧪 Testing Queries

Run these in `php artisan tinker`:

```php
// Test 1: Get all departments with teams
>>> $depts = Department::with('teams')->get();
>>> $depts->each(fn($d) => echo "$d->department_name: {$d->teams->count()} teams\n");

// Test 2: Get IT department staff
>>> $it = Department::where('department_code', 'IT')->first();
>>> $it->staff->pluck('staff_name');

// Test 3: Get team members
>>> $team = Team::first();
>>> $team->staff->pluck('staff_name');

// Test 4: Get staff with department and team
>>> $staff = Staff::with('department', 'team')->first();
>>> echo $staff->staff_name . " - " . $staff->department->department_name . " - " . $staff->team->team_name;

// Test 5: Get team lead's teams
>>> $lead = Staff::first();
>>> $lead->teamsManaged->pluck('team_name');

// Test 6: Create new report
>>> $report = AttendanceReport::create([...]);
```

---

## 🚀 Deployment Instructions

### For Fresh Installation

```powershell
# 1. Navigate to project
cd c:\Users\syami\Desktop\StaffAttendance_System\staff_attendance

# 2. Install dependencies (if needed)
composer install

# 3. Run migrations with fresh data
php artisan migrate:refresh --seed --force

# 4. Clear cache
php artisan cache:clear
php artisan config:clear

# 5. Start server
php artisan serve

# 6. Visit http://localhost:8000
```

### For Existing Installation

```powershell
# 1. Run new migrations only
php artisan migrate --force

# 2. Or run with seed
php artisan migrate:refresh --seed --force

# 3. Clear cache
php artisan cache:clear
```

---

## 📝 Important Notes

1. **Backward Compatibility**: Old staff data will remain, but new department/team assignments needed
2. **Foreign Keys**: Existing staff might have team_id but no matching team record - migrate data manually if needed
3. **Reports**: No reports yet - create them through admin interface after setup
4. **Relationships**: Must use new models for queries to work properly
5. **Migration Order**: Migrations run in order - don't skip any

---

## 🐛 Troubleshooting

### Issue: "Migration Failed"
```
Solution: Check MySQL is running: docker-compose ps
Restart: docker-compose restart
```

### Issue: "Foreign Key Constraint Failed"
```
Solution: Ensure parent tables created first (departments → teams → staff)
Already handled: Migrations run in correct order
```

### Issue: "Model Not Found"
```
Solution: Make sure models are in app/Models/ directory
Namespace: namespace App\Models;
```

### Issue: "Relationships Not Working"
```
Solution: Verify relationships defined in models
Check: $staff->department should not be null
Fix: Update seeder if needed
```

---

**Implementation Date:** November 20, 2025  
**Status:** ✅ Ready to Deploy  
**Next Phase:** Create admin UI for department/team/report management
