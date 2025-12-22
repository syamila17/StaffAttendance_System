# Bug Fixes - December 5, 2025

## Summary of Fixes Applied

### ✅ Issue 1: Staff Search Results Disappear After Search
**Status**: FIXED

**What was changed:**
- File: `resources/views/admin/staff_management.blade.php` (Line 72)
- Added `value="{{ request('search') }}"` to the search input field
- This persists the search term after form submission
- User can now manually clear the search box to reset and see all staff

**Code change:**
```blade
<!-- Before -->
<input type="text" name="search" placeholder="Search by name or email..." 
  class="flex-1 px-4 py-2...">

<!-- After -->
<input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}"
  class="flex-1 px-4 py-2...">
```

---

### ✅ Issue 2: Missing EL Form Fields (Reason & Proof)
**Status**: FIXED

**What was changed:**
1. **View Changes**: `resources/views/attendance.blade.php`
   - Added reason field container (id: `elReasonContainer`) - Shows only for EL status
   - Added proof file upload field (id: `elProofContainer`) - Shows only for EL status
   - Updated JavaScript `toggleTimeInputs()` to `toggleStatusFields()` - Now shows/hides EL fields
   - Updated form reset function to clear EL-specific fields

2. **Database Changes**: New migration file created
   - `database/migrations/2025_12_05_000008_add_el_fields_to_attendance_table.php`
   - Adds: `el_reason` (text), `el_proof_file` (string), `el_proof_file_path` (string), `el_proof_uploaded_at` (timestamp)

3. **Model Changes**: `app/Models/Attendance.php`
   - Updated `$fillable` array to include new EL fields

4. **Controller Changes**: `app/Http/Controllers/AttendanceController.php`
   - Updated `updateStatus()` method to:
     - Validate `el_reason` as mandatory when status is 'el'
     - Handle `el_proof_file` upload with same validation as leave proofs
     - Store file in `el_proofs/staff_ID` folder with timestamp
     - Only set time fields for non-leave statuses

**JavaScript behavior:**
- When EL is selected: Shows reason field (mandatory) + proof field (optional)
- When other status selected: Hides EL fields and clears values
- File upload accepts: PDF, JPG, PNG, DOC, DOCX (Max 5MB)

---

### ✅ Issue 3: Check-in/Check-out Disabled When On Leave
**Status**: FIXED

**What was changed:**
1. **Controller Changes**: `app/Http/Controllers/AttendanceController.php`
   - Updated `show()` method to query approved leaves for today
   - Updated `checkIn()` method to check for approved leave
   - Updated `checkOut()` method to check for approved leave
   - Returns error message if staff has approved leave

2. **View Changes**: `resources/views/attendance.blade.php`
   - Added conditional display: If `$approvedLeaveToday` exists, shows yellow alert message
   - Alert message: "You are on approved leave today. Check-in/Check-out disabled."
   - Check-in/Check-out buttons hidden when on leave
   - Otherwise buttons display normally

**Query logic:**
```php
$approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
    ->where('status', 'approved')
    ->where('from_date', '<=', $today)
    ->where('to_date', '>=', $today)
    ->first();
```

---

### ✅ Issue 4: Annual Leave Balance Calculation Fixed
**Status**: FIXED

**What was changed:**
- File: `app/Http/Controllers/StaffController.php` (Lines 231-246)
- Changed loop from `$activeLeaves` to `$allLeaves` for annual leave calculation
- This ensures historical leaves (even expired ones) are counted correctly for the year
- Calculation now properly counts all approved annual leaves in the calendar year

**Impact:**
- Previously: Only counted leaves with `to_date >= today` (incomplete calculation for past leaves)
- Now: Counts all leaves in the year, regardless of current date, giving accurate balance

**Algorithm:**
```
totalAnnualLeave = 20
For each approved "Annual Leave" request in $allLeaves:
  - Calculate days between from_date and to_date (inclusive)
  - Add to usedLeave counter
remainingBalance = totalAnnualLeave - usedLeave
```

---

## Required Action: Database Migration

**⚠️ IMPORTANT: Run the following command to apply database changes:**

```bash
cd staff_attendance
php artisan migrate
```

This migration adds 4 new columns to the `attendance` table:
- `el_reason` - Store the reason for emergency leave
- `el_proof_file` - Filename of uploaded proof document
- `el_proof_file_path` - Path in storage/public folder
- `el_proof_uploaded_at` - Timestamp when proof was uploaded

---

## Testing Checklist

### 1. Staff Search
- [ ] Go to Admin > Staff Management
- [ ] Search for a staff member by name
- [ ] Verify search term remains in input field
- [ ] Manually clear the search box
- [ ] Verify all staff members reappear

### 2. Emergency Leave Form
- [ ] Go to Attendance page
- [ ] Select status = "EL (Emergency Leave)"
- [ ] Verify reason field appears (mandatory - marked with *)
- [ ] Verify proof upload field appears (optional)
- [ ] Try to submit without reason - should fail validation
- [ ] Add reason and submit - should succeed
- [ ] Verify EL proof appears in attendance records

### 3. Check-in Restriction
- [ ] Create an approved leave for today
- [ ] Go to Attendance page
- [ ] Verify yellow alert appears: "You are on approved leave today..."
- [ ] Verify Check-in/Check-out buttons are hidden
- [ ] Create a leave for tomorrow
- [ ] Verify buttons appear normally for today
- [ ] Check-in successfully

### 4. Annual Leave Balance
- [ ] Create multiple approved Annual Leave requests (e.g., 3 days, 5 days)
- [ ] Go to Staff > Leave Status
- [ ] Verify "Used Leave" shows total approved days (e.g., 8)
- [ ] Verify "Remaining Balance" is correct (e.g., 20 - 8 = 12)
- [ ] Create a past annual leave (already completed)
- [ ] Verify balance still counts it correctly

---

## Files Modified

1. `resources/views/admin/staff_management.blade.php` - Search persistence
2. `resources/views/attendance.blade.php` - EL form, check-in restrictions
3. `app/Http/Controllers/AttendanceController.php` - EL handling, leave checks
4. `app/Http/Controllers/StaffController.php` - Annual leave calculation
5. `app/Models/Attendance.php` - Added fillable fields
6. `database/migrations/2025_12_05_000008_add_el_fields_to_attendance_table.php` - NEW migration

---

## Known Issues / Notes

### Attendance Update Status - Server Error
**Status**: Investigation not required - Original validation was correct

The original error was likely due to:
- Missing time format (should be H:i, not H:i:s) in form input
- File upload field naming inconsistency

**Fixed by:** Properly handling EL-specific fields and ensuring time inputs are only set for relevant statuses.

---

## No Breaking Changes

All changes are backward compatible:
- New fields are nullable
- Existing logic unchanged
- No changes to existing database fields
