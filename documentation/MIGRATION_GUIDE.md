# Quick Start - Apply Bug Fixes

## Step 1: Run Database Migration

Open terminal in the project directory and run:

```bash
cd staff_attendance
php artisan migrate
```

This will add the 4 new columns to the attendance table for EL functionality.

## Step 2: Clear Application Cache (Optional but Recommended)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 3: Test the Fixes

All fixes are now active:

✅ **Staff search results persist** - Search input keeps the value
✅ **EL form has reason and proof fields** - Shows only when EL is selected  
✅ **Check-in/Check-out disabled when on leave** - Yellow alert appears
✅ **Annual leave balance calculated correctly** - Uses all leaves from current year

## Files Changed Summary

| File | Changes | Type |
|------|---------|------|
| `resources/views/admin/staff_management.blade.php` | Search persistence | View |
| `resources/views/attendance.blade.php` | EL fields + leave check | View |
| `app/Http/Controllers/AttendanceController.php` | EL handling + leave validation | Controller |
| `app/Http/Controllers/StaffController.php` | Annual leave calculation fix | Controller |
| `app/Models/Attendance.php` | Added fillable fields | Model |
| `database/migrations/2025_12_05_000008_...` | New migration file | Migration |

## Verification

To verify all changes are applied:

1. Go to Admin > Staff Management → Search and verify term persists
2. Go to Attendance → Select "EL" status → Verify fields appear
3. Create approved leave for today → Go to Attendance → Verify buttons disabled
4. Check Leave Status page → Verify annual leave balance shows correctly

## Rollback (if needed)

```bash
php artisan migrate:rollback --step=1
```

This will remove the EL fields if you need to revert.

## Support

If you encounter any issues:
1. Check `storage/logs/laravel.log` for error details
2. Ensure migrations ran successfully: `php artisan migrate:status`
3. Clear cache: `php artisan cache:clear`
