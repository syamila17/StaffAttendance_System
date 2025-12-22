# Leave Proof File Upload Feature Implementation

## Overview
Staff members can now upload proof documents for **Sick Leave (mandatory)** and **Emergency Leave (optional)**. This feature adds document verification to the leave request system.

## Changes Made

### 1. Database Migration
**File**: `database/migrations/2025_12_03_add_proof_file_to_leave_requests.php`

Added three new columns to `leave_requests` table:
- `proof_file` (VARCHAR 255) - Stores the original filename
- `proof_file_path` (VARCHAR 255) - Stores the full storage path
- `proof_uploaded_at` (TIMESTAMP) - Records when the proof was uploaded

### 2. LeaveRequest Model Updates
**File**: `app/Models/LeaveRequest.php`

- Added new columns to `$fillable` array
- Updated `$casts` to include `proof_uploaded_at` as datetime
- Added helper methods:
  - `isProofRequired()` - Returns true for Sick Leave
  - `isProofOptional()` - Returns true for Emergency Leave
  - `getProofFileUrl()` - Returns asset URL to the file
  - `hasProofFile()` - Checks if proof file exists

### 3. Staff Application Form
**File**: `resources/views/staff_apply_leave.blade.php`

**Changes**:
- Added `enctype="multipart/form-data"` to form for file upload support
- Added conditional proof file upload section that shows/hides based on leave type:
  - **Sick Leave**: Shows "Upload Proof Document *" (mandatory, marked with asterisk)
  - **Emergency Leave**: Shows "Upload Proof Document (Optional)" (optional)
  - **Other Leave Types**: No proof section displayed
- File input accepts: PDF, JPG, PNG, DOC, DOCX (Max 5MB)
- Added file info display showing filename and size
- Added JavaScript to:
  - Show/hide proof section based on selected leave type
  - Validate file size (max 5MB)
  - Display file info when selected

### 4. Staff Controller - File Upload Handler
**File**: `app/Http/Controllers/StaffController.php`

**Updated `storeLeaveRequest()` method**:
- Validates proof file based on leave type
- Stores files to `storage/leave_proofs/staff_{staffId}/`
- Generates unique filenames with timestamp
- Stores proof_file, proof_file_path, and proof_uploaded_at in database
- Shows error if mandatory proof is not uploaded for Sick Leave

**New private methods**:
- `getProofFileValidationRules($leaveType)` - Returns appropriate validation rules
- `validateProofFile($file)` - Validates file type and size

**New public method**:
- `downloadProofFile($id)` - Allows authorized download of proof files
  - Staff can download their own proofs
  - Admins can download any staff's proofs
  - Returns 404 if file doesn't exist
  - Returns 403 if unauthorized

### 5. Admin Leave Requests View
**File**: `resources/views/admin/leave_requests.blade.php`

**Added Proof Column**:
- Shows status of proof file for each leave request
- **For Sick Leave**:
  - ✓ "View" button (blue) if proof uploaded
  - ⚠ "Missing" badge (red) if proof not uploaded
- **For Emergency Leave**:
  - ✓ "View" button (blue) if proof provided
  - "Not provided" if optional proof not included
- **For Other Leaves**: "N/A"

### 6. Staff Leave Status View
**File**: `resources/views/staff_status_leave.blade.php`

**Added Proof Column**:
- Shows proof status in staff's leave history
- **For Sick Leave**:
  - Green "Uploaded" badge if proof attached
  - Red "Required" badge if proof missing
- **For Emergency Leave**:
  - Blue "View" link if proof provided
  - Dash "-" if not provided
- **For Other Leaves**: Dash "-"

### 7. Routes
**File**: `routes/web.php`

Added new route:
```php
Route::get('/staff/leave/{id}/download-proof', [StaffController::class, 'downloadProofFile'])->name('staff.leave.download-proof');
```

### 8. Console Command
**File**: `app/Console/Commands/AddProofFileColumns.php`

Helper command to add columns to existing database:
```bash
php artisan db:add-proof-columns
```

## File Storage Structure
Files are stored in: `storage/app/public/leave_proofs/staff_{staff_id}/`

Example:
```
storage/app/public/leave_proofs/
├── staff_1/
│   ├── 1701619200_medical_certificate.pdf
│   └── 1701619201_doctor_letter.pdf
└── staff_2/
    └── 1701619202_hospital_receipt.jpg
```

## Validation Rules

### Sick Leave (Mandatory)
- File is **required**
- Allowed formats: PDF, JPG, PNG, DOC, DOCX
- Maximum size: 5MB
- Must be submitted with the leave request

### Emergency Leave (Optional)
- File is **optional**
- Same formats and size restrictions as Sick Leave
- Can be added but not required

## User Experience

### For Staff
1. Navigate to "Apply Leave"
2. Select leave type
3. If Sick Leave or Emergency Leave, proof upload section appears
4. Click file input and select document (max 5MB)
5. File info displays with name and size
6. Submit the form

### For Admin
1. Navigate to "Leave Requests"
2. View pending requests with proof status indicator
3. Click "View" button to download and review proof documents
4. Missing proof for Sick Leave shows warning badge
5. Approve or reject based on leave details and proof

## Security Features
- File downloads authenticated (staff/admin only)
- Files stored outside public web root initially
- Original filenames preserved with sanitized storage names
- Staff can only download their own proofs
- Admins have access to all proofs
- File size limited to 5MB to prevent abuse

## Testing Checklist
- [ ] Submit Sick Leave without proof → Should show validation error
- [ ] Submit Sick Leave with valid proof → Should save successfully
- [ ] Submit Emergency Leave without proof → Should save successfully
- [ ] Submit Emergency Leave with proof → Should save successfully
- [ ] Download proof as staff member → Should work for own leaves
- [ ] Download proof as admin → Should work for all leaves
- [ ] Try to download invalid file ID → Should show 404
- [ ] Try to download as unauthorized staff → Should show 403

## API Summary

### LeaveRequest Model Methods
```php
$leave->isProofRequired()      // bool - true if Sick Leave
$leave->isProofOptional()      // bool - true if Emergency Leave
$leave->hasProofFile()         // bool - checks if file exists
$leave->getProofFileUrl()      // string|null - asset URL
```

### Routes
- `staff.leave.download-proof` - Download proof file (GET)

### Controller Methods
- `downloadProofFile($id)` - Download proof from leave request ID

## Dependencies
- Laravel 10.x Storage facade for file operations
- Illuminate\Support\Facades\Storage
- Symfony StreamedResponse (automatic via Laravel download method)

## Notes
- Default public disk is used for storage with 'public' visibility
- Symlink from `public/storage` to `storage/app/public` must be created (Laravel default)
- Files are accessible via `/storage/` URLs when symlink is set up
- Each staff member has their own folder for organized storage
