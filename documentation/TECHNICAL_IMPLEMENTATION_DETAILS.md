# Technical Deep-Dive: Bug Fixes Implementation

## Architecture Changes

### 1. Search Persistence Implementation

**Problem Analysis:**
- Form submission cleared search input value
- User had no way to see search results persist
- Each search reset the UI to default state

**Solution:**
```blade
<!-- Before: Input loses value on every request -->
<input type="text" name="search" placeholder="...">

<!-- After: Input retains value from GET parameter -->
<input type="text" name="search" placeholder="..." value="{{ request('search') }}">
```

**How it works:**
1. User types search term → Form submits via GET
2. Laravel's `request('search')` retrieves the query parameter
3. Value repopulates in input field
4. User can see results and modify search easily

**No controller changes needed** - Works through request helper function

---

### 2. Emergency Leave (EL) Implementation

#### Database Schema Change
```sql
ALTER TABLE attendance ADD COLUMN el_reason TEXT NULL;
ALTER TABLE attendance ADD COLUMN el_proof_file VARCHAR(255) NULL;
ALTER TABLE attendance ADD COLUMN el_proof_file_path VARCHAR(255) NULL;
ALTER TABLE attendance ADD COLUMN el_proof_uploaded_at TIMESTAMP NULL;
```

#### Form Field Structure
```html
<!-- Shown only when status == 'el' -->
<div id="elReasonContainer" style="display: none;">
  <textarea name="el_reason" required></textarea>
</div>

<div id="elProofContainer" style="display: none;">
  <input type="file" name="el_proof_file"></input>
</div>
```

#### JavaScript Logic
```javascript
function toggleStatusFields() {
  const status = document.getElementById('statusSelect').value;
  
  if (status === 'el') {
    // Show EL fields
    document.getElementById('elReasonContainer').style.display = 'block';
    document.getElementById('elProofContainer').style.display = 'block';
    document.querySelector('textarea[name="el_reason"]').required = true;
  } else {
    // Hide EL fields and clear values
    document.getElementById('elReasonContainer').style.display = 'none';
    document.getElementById('elProofContainer').style.display = 'none';
    document.querySelector('textarea[name="el_reason"]').required = false;
    document.querySelector('textarea[name="el_reason"]').value = '';
    document.querySelector('input[name="el_proof_file"]').value = '';
  }
}
```

#### Controller Validation
```php
$validationRules = [
    'status' => 'required|in:present,absent,late,el,on leave,half day',
    'date' => 'required|date',
];

// EL-specific validation
if ($request->input('status') === 'el') {
    $validationRules['el_reason'] = 'required|string|max:1000';
    $validationRules['el_proof_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
}
```

#### File Upload Handling
```php
if ($request->hasFile('el_proof_file')) {
    $file = $request->file('el_proof_file');
    
    // Validate
    $this->validateProofFile($file); // Checks MIME type, file size
    
    // Store
    $staffFolder = 'el_proofs/staff_' . $staffId;
    $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
    $path = $file->storeAs($staffFolder, $fileName, 'public');
    
    // Save metadata
    $attendanceData['el_proof_file'] = $file->getClientOriginalName();
    $attendanceData['el_proof_file_path'] = $path;
    $attendanceData['el_proof_uploaded_at'] = Carbon::now();
}
```

**File Storage Structure:**
```
storage/
├── public/
│   └── el_proofs/
│       ├── staff_1/
│       │   ├── 1638720000_medical_certificate.pdf
│       │   └── 1638720100_letter.jpg
│       └── staff_2/
│           └── 1638720200_document.doc
```

---

### 3. Leave-Based Check-in Restriction

#### Query Design
```php
$approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
    ->where('status', 'approved')
    ->where('from_date', '<=', $today)        // Leave starts today or earlier
    ->where('to_date', '>=', $today)          // Leave ends today or later
    ->first();                                 // Return any matching leave
```

**Query Logic:**
- Finds ANY approved leave that spans today
- `from_date <= today AND to_date >= today` covers the entire leave period
- Example:
  ```
  Leave: Jan 15-20 (Approved)
  Check on Jan 17: from_date(15) <= today(17) AND to_date(20) >= today(17) ✓ FOUND
  Check on Jan 14: from_date(15) <= today(14) ✗ FALSE - leaves tomorrow
  Check on Jan 21: from_date(15) <= today(21) ✓ TRUE but to_date(20) >= today(21) ✗ FALSE
  ```

#### Implementation Points

**In `show()` method:**
```php
$approvedLeaveToday = LeaveRequest::where('staff_id', $staffId)
    ->where('status', 'approved')
    ->where('from_date', '<=', $today)
    ->where('to_date', '>=', $today)
    ->first();

return view('attendance', compact(..., 'approvedLeaveToday'));
```

**In `checkIn()` method:**
```php
if ($approvedLeaveToday) {
    return back()->withErrors(['error' => 'You are on approved leave and cannot check in']);
}
```

**In View:**
```blade
@if($approvedLeaveToday)
    <div class="w-full bg-yellow-500/20 border border-yellow-500 ...">
        You are on approved leave today. Check-in/Check-out disabled.
    </div>
@else
    <!-- Show Check-in/Check-out buttons -->
@endif
```

**Double Validation:**
- Frontend: Buttons hidden (UX)
- Backend: Returns error (Security)

This prevents API-level bypasses where user might try to POST directly.

---

### 4. Annual Leave Calculation Fix

#### Problem Analysis

**Original Code:**
```php
foreach ($activeLeaves->where('status', 'approved') as $leave) {
    // $activeLeaves filters: to_date >= today
    // Problem: Historical leaves not counted
}
```

**Issue Example:**
```
Today: December 5
User took:
- Jan 10-15: 5 days (APPROVED, EXPIRED) ← Won't be counted!
- March 20-23: 3 days (APPROVED, EXPIRED) ← Won't be counted!
- Dec 1-3: 2 days (APPROVED, EXPIRED) ← Won't be counted!

Result with bug: Shows 0 used, 20 remaining (WRONG!)
Result with fix: Shows 10 used, 10 remaining (CORRECT!)
```

**Solution:**
```php
// Use $allLeaves instead of $activeLeaves
foreach ($allLeaves->where('status', 'approved') as $leave) {
    if ($leave->leave_type === 'Annual Leave' 
        && $leave->from_date <= $currentYearEnd 
        && $leave->to_date >= $currentYearStart) {
        
        $daysUsed = $start->diffInDays($end) + 1;
        $usedLeave += $daysUsed;
    }
}
```

**Why this works:**
- `$allLeaves` includes all leaves regardless of to_date
- `$activeLeaves` only includes leaves with to_date >= today (incomplete)
- We still filter by leave_type and year correctly
- Results in complete historical count

#### Date Overlap Logic
```
Year 2025: Jan 1 - Dec 31

Leave 1: Jan 10 - Mar 15
  from_date(Jan 10) <= currentYearEnd(Dec 31) ✓
  to_date(Mar 15) >= currentYearStart(Jan 1) ✓
  INCLUDED

Leave 2: Dec 15 - Jan 20 (crosses year boundary)
  from_date(Dec 15, 2024) <= currentYearEnd(Dec 31, 2025) ✓
  to_date(Jan 20, 2025) >= currentYearStart(Jan 1, 2025) ✓
  INCLUDED

Leave 3: Mar 10 - Apr 15 (in current year)
  from_date(Mar 10) <= currentYearEnd(Dec 31) ✓
  to_date(Apr 15) >= currentYearStart(Jan 1) ✓
  INCLUDED
```

#### Calculation Example
```php
$start = max($leave->from_date, $currentYearStart);
$end = min($leave->to_date, $currentYearEnd);
$daysUsed = $start->diffInDays($end) + 1;
```

**Why +1?**
- `diffInDays()` returns the difference between two dates
- Jan 10 to Jan 10 = 0 days difference, but it's 1 day of leave
- Adding 1 makes it inclusive of both start and end dates

Example:
```
Jan 10 to Jan 12:
  diffInDays() = 2
  +1 = 3 days (Jan 10, 11, 12)
```

---

## Error Handling

### Validation Errors
```php
$validated = $request->validate($validationRules);
// If validation fails, automatically redirects back with errors
// User sees error message in session
```

### File Upload Errors
```php
try {
    $this->validateProofFile($file);
    $path = $file->storeAs(...);
} catch (\Exception $e) {
    return redirect()
        ->withErrors(['el_proof_file' => 'Error uploading file: ' . $e->getMessage()]);
}
```

### Leave Query Errors
```php
// If no leave found, $approvedLeaveToday is null
// Checks use: if ($approvedLeaveToday) - safe null check
```

---

## Performance Considerations

### Query Optimization

**Attendance Status Update:**
- Uses `firstOrCreate()` + conditional `update()` - single DB query for each request
- No N+1 query problems

**Leave Status Page:**
- Single query for all leaves
- Filtering in PHP (acceptable for small datasets)
- Could add DB-level filter for large datasets:
  ```php
  LeaveRequest::where('staff_id', $staffId)
      ->where('status', 'approved')
      ->where('to_date', '>=', today())
      ->get();
  ```

**Annual Leave Calculation:**
- Iterates through filtered collection in PHP
- Database carries the bulk load
- No additional queries needed

### File Storage

**Before Upload:**
- Validates file in memory (no disk write yet)
- Size check: 5MB limit prevents large uploads

**After Upload:**
- Files stored with timestamp prefix (prevents collisions)
- Organized by staff ID (easy cleanup/backup)
- Public disk accessible to users

---

## Testing Strategy

### Unit Tests (if implemented)
```php
// Test annual leave calculation
$leave1 = factory(LeaveRequest::class)->create([
    'leave_type' => 'Annual Leave',
    'from_date' => Carbon::now()->subDays(30),
    'to_date' => Carbon::now()->subDays(25),
    'status' => 'approved'
]);
// Assert: usedLeave = 5
```

### Integration Tests
```php
// Test EL form submission with proof
$response = $this->post(route('attendance.updateStatus'), [
    'status' => 'el',
    'date' => today(),
    'el_reason' => 'Medical emergency',
    'el_proof_file' => UploadedFile::fake()->create('doc.pdf')
]);
// Assert: File stored, attendance record created
```

### Acceptance Tests
```gherkin
Scenario: Staff cannot check-in when on leave
    Given a staff member has an approved leave today
    When they visit the attendance page
    Then the check-in button should be hidden
    And an alert message should display
```

---

## Rollback Plan

If issues occur:

```bash
# Revert to previous state
php artisan migrate:rollback --step=1

# This will:
# - Remove el_reason column
# - Remove el_proof_file column
# - Remove el_proof_file_path column
# - Remove el_proof_uploaded_at column
# - Attendance table back to original schema
```

**Important:**
- Existing EL attendance records won't be deleted
- Data will be retained but new EL fields will be empty
- If fully reverting, delete uploaded files manually:
  ```bash
  rm -rf storage/public/el_proofs/
  ```

---

## Monitoring

### Error Tracking
- Check `storage/logs/laravel.log` for:
  - File upload failures
  - Validation errors
  - Database constraint violations

### Performance Monitoring
- File uploads: Monitor `storage/` disk usage
- Queries: Check MySQL slow query log
- Memory: Watch PHP memory usage

### User-Facing Issues
- Search not persisting: Check browser cache/cookies
- EL fields not showing: Check JavaScript errors (F12 > Console)
- Leave checks failing: Verify LeaveRequest records exist with correct dates

---

## Future Improvements

1. **Exclude Weekends**: Modify annual leave calculation to skip Saturdays/Sundays
2. **File Preview**: Add inline preview for proof documents in admin panel
3. **Bulk Leave Import**: Admin ability to import historical leave data
4. **Leave Balance Sync**: Fetch balance from separate staff_leave_entitlements table
5. **Attendance Reports**: Generate PDF attendance reports with EL details
