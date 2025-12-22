# Staff Leave Management - Enhanced Features Implementation

**Date**: December 3, 2025  
**Status**: ✅ Complete

## Summary of Changes

Four major enhancements have been implemented to improve the staff leave management system:

---

## 1. **Mandatory Reason for "Other" Leave Type**

### Files Modified:
- `resources/views/staff_apply_leave.blade.php`
- `app/Http/Controllers/StaffController.php`

### Features:
- When staff selects "Other" as leave type, the reason field becomes **mandatory**
- Field label changes to show asterisk (*) and placeholder updates
- Form validation enforces required reason for "Other" leave type
- JavaScript dynamically updates field requirements based on selection

### Frontend Changes:
```javascript
if (leaveType === 'Other') {
    reasonField.required = true;
    reasonRequirement.textContent = '*';
    reasonField.placeholder = 'Please explain the reason for this leave request (Required)';
}
```

### Backend Validation:
```php
'reason' => $request->leave_type === 'Other' ? 'required|string|max:1000' : 'nullable|string|max:1000'
```

### User Experience:
- Staff can see immediately if reason is required based on leave type
- Prevents accidental submission without proper explanation for "Other" leaves
- Error messages guide staff to provide reason if forgotten

---

## 2. **Multiple Concurrent Staff Logins**

### Files Created:
- `database/migrations/2025_12_03_create_staff_sessions_table.php`
- `app/Models/StaffSession.php`
- `app/Console/Commands/CreateStaffSessionsTable.php`

### Files Modified:
- `app/Http/Controllers/AuthController.php`

### Database Structure:
New `staff_sessions` table tracks:
- `staff_id` - Reference to staff member
- `session_id` - Unique session identifier
- `ip_address` - Login device IP
- `user_agent` - Browser/device information
- `logged_in_at` - Login timestamp
- `last_activity_at` - Last activity timestamp

### Features:
- **Multiple concurrent logins** from different devices/browsers for same staff member
- Each login session independently tracked in database
- Session information recorded:
  - Device IP address
  - Browser/device user agent
  - Login timestamp
  - Last activity time

### Implementation:
```php
// On login: Create session record
StaffSession::create([
    'staff_id' => $staff->staff_id,
    'session_id' => $sessionId,
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'logged_in_at' => Carbon::now(),
    'last_activity_at' => Carbon::now(),
]);

// On logout: Delete session record
StaffSession::where('session_id', $sessionId)->delete();
```

### Benefits:
- Staff can be logged in from multiple devices simultaneously
- No session conflicts between different browsers/devices
- Track staff login patterns and activity
- Support for mobile and desktop concurrent access

---

## 3. **Staff Leave Status Notification Badge**

### Files:
- `resources/views/staff_dashboard.blade.php` (already had structure, now active)

### Features:
- **Red badge** on "Leave Status" navigation link
- Shows count of **pending leave status updates** (approved/rejected)
- Badge displays only when there are unviewed status changes
- Auto-refreshes every 30 seconds

### Functionality:
- Mirrors admin panel notification style
- Updates automatically without page reload
- Shows pending approvals/rejections awaiting staff review
- Disappears once staff views leave status page

### JavaScript:
```javascript
async function loadNotificationBadge() {
  const response = await fetch('{{ route("staff.leave.notifications") }}');
  const data = await response.json();
  const badge = document.getElementById('notificationBadge');
  
  if (data.count > 0) {
    badge.textContent = data.count;
    badge.classList.remove('hidden');
  }
}

// Refresh every 30 seconds
setInterval(loadNotificationBadge, 30000);
```

### Visual Indicator:
- Badge location: Top-right of "Leave Status" link
- Color: Red (indicates action needed)
- Auto-hides when count is 0

---

## 4. **Proof File Viewer Modal**

### Files Modified:
- `resources/views/admin/leave_requests.blade.php`
- `resources/views/staff_status_leave.blade.php`
- `app/Http/Controllers/StaffController.php` (existing route)

### Features:
- **Modal popup** for viewing proof files without downloading
- **Download button** available but optional
- Supports multiple file formats:
  - **PDFs**: Embedded preview
  - **Images** (JPG, PNG, JPEG): Display in modal
  - **Documents** (DOC, DOCX): View-only message with download option
  
### User Interface:
```
┌─────────────────────────────────────────────┐
│ Proof Document                           ✕  │
│ medical_certificate.pdf                     │
├─────────────────────────────────────────────┤
│                                             │
│         [PDF/Image Preview Display]         │
│                                             │
├─────────────────────────────────────────────┤
│ Previewing document. Use download to save. │
│                [Close]  [Download]         │
└─────────────────────────────────────────────┘
```

### Implementation:
```javascript
function openProofModal(leaveId, fileName) {
  // Get file extension
  const ext = fileName.split('.').pop().toLowerCase();
  
  // Show appropriate preview
  if (ext === 'pdf') {
    // Embed PDF in iframe
  } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
    // Show image in iframe
  } else {
    // Show message: Preview not available, please download
  }
  
  // Set download button onclick
  downloadBtn.onclick = () => window.location.href = proofUrl;
}
```

### Admin View:
- "View" button (blue) replaces direct download link
- Click opens modal with file preview
- Optional download without forcing it
- Maintains system for view-only option

### Staff View:
- Same modal functionality
- Staff can view their own proof files
- Download button available if needed
- Integration with leave request history

### Benefits:
- **Privacy**: Files stay on server, not forced downloads
- **Flexibility**: Choose to view or download
- **Preview**: See content before downloading
- **User Control**: Decide what to do with file

---

## Database Changes Summary

### New Tables:
1. **staff_sessions**
   - Tracks concurrent login sessions
   - Records device and IP information
   - Enables multi-device login support

### Modified Tables:
1. **leave_requests** (columns added in previous implementation)
   - `proof_file` - Original filename
   - `proof_file_path` - Storage path
   - `proof_uploaded_at` - Upload timestamp

---

## Validation Rules

### Leave Request Form:
- **Leave Type**: Required, must select from dropdown
- **From Date**: Required, must be today or future
- **To Date**: Required, must be >= From Date
- **Reason**: 
  - Optional for: Annual, Sick, Emergency, Personal, Compassionate
  - **Mandatory for: Other** ← NEW
- **Proof File**:
  - Mandatory for: Sick Leave
  - Optional for: Emergency Leave
  - Not applicable for: Other leave types

---

## API Endpoints

### Authentication:
- `POST /login` - Staff login (creates session record)
- `GET /logout` - Staff logout (deletes session record)

### Leave Management:
- `GET /staff/leave/{id}/download-proof` - Download/view proof file
- `GET /staff/leave-notifications` - Get notification count

---

## Testing Checklist

### Feature 1: Mandatory Reason for "Other"
- [ ] Select "Other" leave type → reason field shows *
- [ ] Try submit without reason → error message appears
- [ ] Submit with reason → works correctly
- [ ] Select different leave type → asterisk disappears

### Feature 2: Multiple Logins
- [ ] Login from browser 1 → staff page loads
- [ ] Login from browser 2 with same account → session created
- [ ] Both browsers still active → no session conflicts
- [ ] Logout from browser 1 → session removed
- [ ] Browser 2 still works → independent session

### Feature 3: Notification Badge
- [ ] Submit leave request as staff → appears pending
- [ ] Admin approves → badge shows on staff dashboard
- [ ] Click badge → leaves status page → badge hides
- [ ] Badge updates every 30 seconds

### Feature 4: Proof Viewer Modal
- [ ] Admin clicks "View" button → modal opens
- [ ] PDF file → preview in modal
- [ ] Image file → displays in modal
- [ ] Doc file → shows "download to view" message
- [ ] Click Download → file downloads
- [ ] Click Close → modal closes
- [ ] Click outside modal → modal closes
- [ ] Staff can view own proofs

---

## Performance Considerations

- Session tracking adds minimal database overhead
- Notification badge refreshes every 30 seconds (configurable)
- Modal uses efficient iframe/image rendering
- File previews don't re-download on every view (browser cache)

---

## Security Measures

1. **Session Tracking**:
   - Sessions tied to staff_id (verified authentication)
   - Unique session_id per login
   - IP and user agent stored for audit trail

2. **File Viewing**:
   - Authorization check: staff can only view own files or admins can view any
   - 404 error if file doesn't exist
   - 403 error if unauthorized access attempted

3. **Leave Validation**:
   - Server-side validation enforces mandatory reason for "Other"
   - Client-side validation provides immediate feedback
   - File format and size restrictions maintained

---

## Deployment Steps

1. ✅ Database migrations applied
2. ✅ Models created
3. ✅ Controllers updated
4. ✅ Views enhanced with modals
5. ✅ Cache cleared
6. ✅ Ready for testing

## Testing Environment Setup

To test the changes:

1. **Multiple Logins**:
   ```bash
   # Open 2 browser windows/tabs
   # Browser 1: Login as staff A
   # Browser 2: Login as staff A
   # Both should work without conflicts
   ```

2. **Leave Notifications**:
   ```bash
   # Staff: Submit leave request
   # Admin: Approve request
   # Staff: Should see badge on dashboard (or refresh page)
   ```

3. **Proof Files**:
   ```bash
   # Submit sick leave with PDF proof
   # Admin: Click View → Modal shows PDF preview
   # Click Download → PDF downloads
   ```

4. **Mandatory Reason**:
   ```bash
   # Select "Other" leave type
   # Try submit without reason → Error appears
   # Add reason → Submit works
   ```

---

## Notes

- All caches have been cleared
- System ready for immediate testing
- No breaking changes to existing functionality
- Backward compatible with current leave management
- Staff can safely logout from any device without affecting others
