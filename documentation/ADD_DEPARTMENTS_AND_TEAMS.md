# How to Add/Change Departments and Teams

## Method 1: Using PHP Artisan Tinker (Interactive)

```bash
cd staff_attendance
php artisan tinker
```

Then run these commands:

### Add a Department
```php
App\Models\Department::create([
    'department_name' => 'Your Department Name',
    'department_code' => 'DEPT_CODE',
    'description' => 'Department Description',
    'location' => 'Office Location',
    'status' => 'active'
]);
```

### Add a Team
```php
App\Models\Team::create([
    'team_name' => 'Your Team Name',
    'team_code' => 'TEAM_CODE',
    'department_id' => 1,  // Replace with actual department_id
    'description' => 'Team Description',
    'status' => 'active'
]);
```

### View All Departments
```php
App\Models\Department::all();
```

### View All Teams
```php
App\Models\Team::all();
```

### View Teams for a Department
```php
App\Models\Department::find(1)->teams();
```

---

## Method 2: Using Database GUI Tool (phpMyAdmin)

1. Open phpMyAdmin
2. Go to your `staff_attendance` database
3. Find the `departments` table
4. Click "Insert" and add your departments
5. Then add teams to the `teams` table with the corresponding department_id

---

## Method 3: Using a Seeder (Automatic)

Create a seeder file:

```bash
php artisan make:seeder DepartmentTeamSeeder
```

Then edit the file in `database/seeders/DepartmentTeamSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Team;

class DepartmentTeamSeeder extends Seeder
{
    public function run()
    {
        // Create Departments
        $it = Department::create([
            'department_name' => 'IT',
            'department_code' => 'IT',
            'description' => 'Information Technology',
            'location' => 'Building A',
            'status' => 'active'
        ]);

        $hr = Department::create([
            'department_name' => 'Human Resources',
            'department_code' => 'HR',
            'description' => 'Human Resources',
            'location' => 'Building B',
            'status' => 'active'
        ]);

        // Create Teams for IT Department
        Team::create([
            'team_name' => 'Backend Development',
            'team_code' => 'BACKEND',
            'department_id' => $it->department_id,
            'description' => 'Backend Development Team',
            'status' => 'active'
        ]);

        Team::create([
            'team_name' => 'Frontend Development',
            'team_code' => 'FRONTEND',
            'department_id' => $it->department_id,
            'description' => 'Frontend Development Team',
            'status' => 'active'
        ]);

        // Create Teams for HR Department
        Team::create([
            'team_name' => 'Recruitment',
            'team_code' => 'RECRUITMENT',
            'department_id' => $hr->department_id,
            'description' => 'Recruitment Team',
            'status' => 'active'
        ]);
    }
}
```

Then run:
```bash
php artisan db:seed --class=DepartmentTeamSeeder
```

---

## Current Issue Explanation

The dropdown lists fetch data from the database, not from the code directly. This is the correct architecture because:
- ✅ Data is centralized in the database
- ✅ Easy to manage through UI or database tools
- ✅ Multiple users can share the same data
- ✅ Data persists across application restarts

If departments/teams don't appear in the dropdown, it means:
1. They haven't been added to the database yet
2. Their `status` is not set to `'active'`
3. There's a relationship issue between departments and teams

---

## Quick Test: Check Current Data

Run this command:

```bash
php artisan tinker
```

Then:
```php
App\Models\Department::where('status', 'active')->get();
```

This will show all active departments. If it's empty, you need to add some departments first.
