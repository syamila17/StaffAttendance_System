# STAFF ATTENDANCE MANAGEMENT SYSTEM - COMPLETE SYSTEM FLOWCHART

## 1. SYSTEM ENTRY POINT

```
┌─────────────────────────────────────────┐
│   User Access System                    │
│   localhost:8000                        │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│   Route Middleware Check                │
│   Is request authenticated?             │
└────────┬──────────────────────┬─────────┘
         │                      │
      YES│                      │NO
         │                      └──────┐
         │                             │
         ▼                             ▼
   [Redirect to              ┌──────────────────────┐
    Authenticated            │ Redirect to /login   │
    Dashboard]               │ (Login Page)         │
                             └──────────────────────┘
```

---

## 2. AUTHENTICATION FLOWS

### 2.1 STAFF LOGIN FLOW

```
┌──────────────────────────────────────────────────────────────┐
│                   STAFF LOGIN PROCESS                        │
└──────────────────────────────────────────────────────────────┘

START
  │
  ├─► GET /login
  │   └─► Display login.blade.php
  │       - Staff ID/Email input field
  │       - Password input field
  │       - Language selector (EN/MS)
  │
  ▼
USER ENTERS CREDENTIALS
  │
  ├─► POST /login
  │   └─► AuthController@login()
  │
  ▼
VALIDATE INPUT FORMAT
  │
  ├─► If contains '@' → Treat as EMAIL
  │   └─► Validate email format
  │
  ├─► Else → Treat as STAFF_ID
  │   └─► Validate staff_id format (st\d+)
  │
  ▼
QUERY DATABASE (staff table)
  │
  ├─► If EMAIL:
  │   └─► WHERE staff_email = {input} CASE-INSENSITIVE
  │
  ├─► If STAFF_ID:
  │   └─► WHERE staff_id = {normalized_input} LOWERCASE
  │
  ▼
STAFF FOUND?
  │
  ├─ YES ─► Verify Password (Hash::check())
  │  │
  │  ├─ PASSWORD MATCH?
  │  │  │
  │  │  ├─ YES ─► CREATE SESSION
  │  │  │  │
  │  │  │  ├─► session(['staff_id' => staff_id])
  │  │  │  ├─► session(['staff_name' => staff_name])
  │  │  │  ├─► session(['staff_email' => staff_email])
  │  │  │  ├─► session(['login_time' => now()])
  │  │  │  │
  │  │  │  ├─► CREATE StaffSession RECORD
  │  │  │  │   - staff_id
  │  │  │  │   - session_id
  │  │  │  │   - ip_address
  │  │  │  │   - user_agent
  │  │  │  │   - logged_in_at
  │  │  │  │
  │  │  │  ├─► LOG LOGIN SUCCESS
  │  │  │  │
  │  │  │  └─► REDIRECT → /staff_dashboard ✅
  │  │  │
  │  │  └─ NO ──► LOG FAILED ATTEMPT
  │  │           RETURN WITH ERROR
  │  │           "Invalid Staff ID or Password"
  │  │
  │  └─ (Back to login form)
  │
  └─ NO ──► LOG FAILED ATTEMPT
          RETURN WITH ERROR
          "Staff ID/Email not found"
          (Back to login form)
```

### 2.2 ADMIN LOGIN FLOW (SIMILAR STRUCTURE)

```
┌──────────────────────────────────────────────────────────────┐
│                   ADMIN LOGIN PROCESS                        │
└──────────────────────────────────────────────────────────────┘

START
  │
  ├─► GET /admin_login
  │   └─► Display admin_login.blade.php
  │       - Admin Email input field
  │       - Password input field
  │       - Language selector (EN/MS)
  │
  ▼
USER ENTERS CREDENTIALS
  │
  ├─► POST /admin_login
  │   └─► AdminController@login()
  │
  ▼
VALIDATE INPUT
  │
  ├─► Is valid email format?
  │
  ▼
QUERY DATABASE (admin table)
  │
  ├─► WHERE admin_email = {input}
  │
  ▼
ADMIN FOUND?
  │
  ├─ YES ─► Verify Password (Hash::check())
  │  │
  │  ├─ PASSWORD MATCH?
  │  │  │
  │  │  ├─ YES ─► CREATE SESSION
  │  │  │  │
  │  │  │  ├─► session(['admin_id' => admin_id])
  │  │  │  ├─► session(['admin_name' => admin_name])
  │  │  │  ├─► session(['admin_email' => admin_email])
  │  │  │  ├─► session(['login_time' => now()])
  │  │  │  │
  │  │  │  ├─► LOG LOGIN SUCCESS
  │  │  │  │
  │  │  │  └─► REDIRECT → /admin_dashboard ✅
  │  │  │
  │  │  └─ NO ──► LOG FAILED ATTEMPT
  │  │           RETURN WITH ERROR
  │  │           "Incorrect password"
  │  │
  │  └─ (Back to login form)
  │
  └─ NO ──► LOG FAILED ATTEMPT
          RETURN WITH ERROR
          "Admin email not found"
          (Back to login form)
```

---

## 3. STAFF PORTAL FLOWS

### 3.1 STAFF DASHBOARD FLOW

```
┌──────────────────────────────────────────────────────────────┐
│              STAFF DASHBOARD (/staff_dashboard)              │
└──────────────────────────────────────────────────────────────┘

AUTHENTICATE
  │
  ├─► staff.auth middleware
  │   └─► Verify session['staff_id'] exists
  │
  ▼
GET DISPLAY DATA
  │
  ├─► GET SESSION DATA
  │   ├─► staff_id
  │   ├─► staff_name
  │   ├─► staff_email
  │   └─► login_time
  │
  ├─► QUERY TODAY'S ATTENDANCE
  │   ├─► WHERE staff_id = {session_staff_id}
  │   ├─► WHERE attendance_date = TODAY
  │   └─► Get check_in_time, check_out_time, status
  │
  ├─► CHECK FOR APPROVED LEAVE TODAY
  │   ├─► WHERE staff_id = {session_staff_id}
  │   ├─► WHERE status = 'approved'
  │   ├─► WHERE from_date <= TODAY
  │   └─► WHERE to_date >= TODAY
  │
  ├─► GET MONTHLY STATISTICS
  │   ├─► Count Present (status = 'present')
  │   ├─► Count Absent (status = 'absent')
  │   ├─► Count Late (status = 'late')
  │   ├─► Count EL (status = 'el')
  │   ├─► Count On Leave (status = 'on leave')
  │   └─► Count Half Day (status = 'half day')
  │
  ├─► GET STAFF PROFILE
  │   ├─► Query StaffProfile table
  │   └─► Get phone, address, designation, hire_date
  │
  ├─► GET RECENT ATTENDANCE
  │   ├─► Last 30 records ordered by date DESC
  │   └─► For historical view
  │
  └─► GET LEAVE BALANCE
      ├─► Total annual leave = 20
      ├─► Count used annual leaves (approved, this year)
      └─► remaining = total - used
      
DISPLAY DASHBOARD
  │
  ├─► Show Welcome Card
  │   └─► Hello, {staff_name}!
  │
  ├─► Show Today's Status Card
  │   ├─ If on approved leave:
  │   │  └─► Yellow alert "You are on approved leave"
  │   │      Buttons disabled
  │   │
  │   └─ If not on leave:
  │      ├─► Check-in Button
  │      ├─► Check-out Button (if checked in)
  │      └─► Status: {current_status}
  │
  ├─► Show Monthly Statistics
  │   ├─► Pie chart of attendance breakdown
  │   ├─► Bar chart of trends
  │   └─► Month selector (dropdown)
  │
  ├─► Show Leave Balance Card
  │   ├─► Used: {used_leaves}
  │   ├─► Remaining: {remaining_leaves}
  │   └─► Action: Apply for Leave button
  │
  ├─► Show Navigation Menu
  │   ├─► Attendance Page
  │   ├─► Profile Page
  │   ├─► Apply Leave Page
  │   ├─► Leave Status Page
  │   └─► Logout
  │
  └─► Show Recent Attendance Table
      └─► Last 30 days' attendance records
```

### 3.2 CHECK-IN / CHECK-OUT FLOW

```
┌──────────────────────────────────────────────────────────────┐
│         CHECK-IN & CHECK-OUT FLOW (/attendance)             │
└──────────────────────────────────────────────────────────────┘

DISPLAY ATTENDANCE PAGE
  │
  ├─► GET /attendance
  │   └─► AttendanceController@show()
  │
  ▼
AUTHENTICATE & GET DATA
  │
  ├─► Verify staff_id from session
  │
  ├─► Query Today's Attendance Record
  │   ├─► staff_id = session_staff_id
  │   └─► attendance_date = TODAY
  │
  ├─► Check for Approved Leave Today
  │   ├─► WHERE status = 'approved'
  │   ├─► WHERE from_date <= TODAY
  │   └─► WHERE to_date >= TODAY
  │
  └─► Get Recent Attendance (last 30 days)

DISPLAY FORM
  │
  ├─► If On Approved Leave:
  │   ├─► Show yellow alert: "You are on approved leave"
  │   ├─► Disable check-in/check-out buttons
  │   └─► Show leave details
  │
  └─► If Not On Leave:
      ├─► If NOT checked in yet:
      │   └─► Show "CHECK-IN" Button
      │
      ├─► If checked in but NOT checked out:
      │   ├─► Show current check-in time
      │   └─► Show "CHECK-OUT" Button
      │
      └─► If both checked in AND out:
          ├─► Show check-in time
          ├─► Show check-out time
          └─► Show "Done for today"

USER CLICKS CHECK-IN
  │
  ├─► POST /attendance/check-in
  │   └─► AttendanceController@checkIn()
  │
  ▼
VERIFY CONDITIONS
  │
  ├─► Check for approved leave today
  │   ├─ If yes: Return error "Cannot check-in on leave"
  │   └─ If no: Continue
  │
  ├─► Check if already marked as absent
  │   ├─ If yes: Return error "Cannot check-in when absent"
  │   └─ If no: Continue
  │
  ▼
CREATE/UPDATE ATTENDANCE RECORD
  │
  ├─► firstOrCreate(
  │   ├─ staff_id = session_staff_id
  │   └─ attendance_date = TODAY
  │ )
  │
  ├─► SET check_in_time = NOW (HH:MM:SS format)
  │
  ├─► SET status = 'present'
  │
  └─► SAVE to database

SUCCESS
  │
  ├─► Log check-in action
  │
  ├─► Return success message
  │   └─► "Check-in successful at HH:MM:SS"
  │
  └─► Refresh attendance page

USER CLICKS CHECK-OUT (SIMILAR)
  │
  ├─► POST /attendance/check-out
  │   └─► AttendanceController@checkOut()
  │
  ▼
VERIFY CHECKED IN
  │
  ├─► Find today's attendance record
  │   ├─ If no record: Return error
  │   └─ If found: Continue
  │
  ├─► Check if already checked out
  │   ├─ If yes: Return error
  │   └─ If no: Continue
  │
  ▼
UPDATE ATTENDANCE RECORD
  │
  ├─► SET check_out_time = NOW (HH:MM:SS)
  │
  └─► SAVE to database

SUCCESS
  │
  ├─► Log check-out action
  │
  ├─► Return success message
  │   └─► "Check-out successful at HH:MM:SS"
  │
  └─► Refresh attendance page
```

### 3.3 UPDATE ATTENDANCE STATUS FLOW (ADMIN/SELF)

```
┌──────────────────────────────────────────────────────────────┐
│    UPDATE ATTENDANCE STATUS (/attendance/update-status)    │
│    (Mark as Present, Absent, Late, EL, Half Day, etc)     │
└──────────────────────────────────────────────────────────────┘

USER SELECTS STATUS
  │
  ├─► From dropdown or form
  │
  ▼
OPTIONS
  │
  ├─ "present" or "late":
  │  │
  │  ├─► Show time picker for check-in
  │  ├─► Show time picker for check-out
  │  └─► Time is mandatory
  │
  ├─ "absent":
  │  │
  │  └─► Hide all time fields
  │
  ├─ "half day":
  │  │
  │  ├─► Show time picker for check-in
  │  └─► Time is mandatory
  │
  ├─ "el" (Emergency Leave):
  │  │
  │  ├─► Show reason text field (MANDATORY)
  │  ├─► Show file upload field (OPTIONAL)
  │  │   ├─► Accept: PDF, JPG, PNG, DOC, DOCX
  │  │   └─► Max size: 5MB
  │  └─► Hide check-in/out time fields
  │
  ├─ "on leave":
  │  │
  │  └─► Automatically handled by approved leave requests
  │
  └─ "work from home":
      │
      └─► Special status flag
      
SUBMIT FORM
  │
  ├─► POST /attendance/update-status
  │   └─► AttendanceController@updateStatus()
  │
  ▼
VALIDATION
  │
  ├─► If status = 'el':
  │   ├─► Reason is mandatory
  │   ├─► File upload is optional
  │   ├─► Validate file if provided
  │   └─► No time fields for EL
  │
  ├─► If status = 'present' or 'late':
  │   ├─► Check-in time is mandatory
  │   └─► Check-out time is mandatory
  │
  ├─► If status = 'half day':
  │   └─► Check-in time is mandatory
  │
  └─► If status = 'absent':
      └─► No validation needed
      
SAVE TO DATABASE
  │
  ├─► Find/Create attendance record
  │   ├─ staff_id = {staff_id}
  │   └─ attendance_date = {date}
  │
  ├─► IF status = 'el':
  │   ├─► el_reason = {reason}
  │   ├─► el_proof_file = {filename} (if provided)
  │   └─► el_proof_file_path = {storage_path}
  │
  ├─► ELSE:
  │   ├─► check_in_time = {time}
  │   └─► check_out_time = {time}
  │
  ├─► status = {selected_status}
  │
  └─► remarks = {any_notes}

SUCCESS
  │
  ├─► Log status change
  │
  ├─► Return success message
  │
  └─► Refresh page
```

### 3.4 LEAVE REQUEST FLOW

```
┌──────────────────────────────────────────────────────────────┐
│         APPLY FOR LEAVE (/staff/apply-leave)                │
└──────────────────────────────────────────────────────────────┘

DISPLAY LEAVE FORM
  │
  ├─► GET /staff/apply-leave
  │   └─► StaffController@showApplyLeave()
  │
  ▼
FORM FIELDS
  │
  ├─► Leave Type Dropdown
  │   ├─ "Annual Leave" - Requires 20 days/year
  │   ├─ "Sick Leave" - Requires medical certificate
  │   ├─ "Emergency Leave" - Can be optional certificate
  │   ├─ "Compassionate Leave"
  │   └─ Other types
  │
  ├─► From Date Picker
  │   └─► Cannot be in the past
  │
  ├─► To Date Picker
  │   ├─► Must be >= From Date
  │   └─► Calculate duration
  │
  ├─► Reason Text Area
  │   └─► Mandatory
  │
  ├─► Proof File Upload
  │   ├─► If "Sick Leave": MANDATORY
  │   ├─► If "Emergency Leave": OPTIONAL
  │   ├─► Accept: PDF, JPG, PNG, DOC, DOCX
  │   └─► Max size: 5MB
  │
  └─► Submit Button
  
FORM SUBMISSION
  │
  ├─► POST /staff/leave
  │   └─► StaffController@storeLeaveRequest()
  │
  ▼
VALIDATION
  │
  ├─► Staff ID from session
  │
  ├─► Leave Type is valid
  │
  ├─► From Date is not in past
  │
  ├─► To Date >= From Date
  │
  ├─► Reason is provided
  │
  ├─► Proof file if required
  │   ├─► File must be uploaded
  │   ├─► File type validation
  │   └─► File size <= 5MB
  │
  ├─► Check leave balance (for Annual Leave)
  │   ├─► Calculate duration in days
  │   ├─► Get remaining balance
  │   ├─ If insufficient: Return error
  │   └─ If sufficient: Continue
  │
  └─► Check for overlapping leaves
      ├─► Query existing leave requests for date range
      └─ If overlap exists: May allow or reject per rules

SAVE LEAVE REQUEST
  │
  ├─► CREATE LeaveRequest RECORD
  │   ├─ staff_id = {staff_id}
  │   ├─ leave_type = {type}
  │   ├─ from_date = {date}
  │   ├─ to_date = {date}
  │   ├─ reason = {reason}
  │   ├─ status = 'pending'
  │   ├─ proof_file = {filename} (if provided)
  │   └─ proof_file_path = {path}
  │
  └─► STORE in database

HANDLE FILE UPLOAD
  │
  ├─► If file provided:
  │   ├─► Generate unique filename
  │   │   └─► Format: {staff_id}_{timestamp}_{originalname}
  │   ├─► Store in storage/app/leave_proofs/{staff_id}/
  │   ├─► Create symlink for public access
  │   └─► Save path in database
  │
  └─► If no file: Skip

SUCCESS
  │
  ├─► Send notification email to admin
  │   └─► New leave request pending approval
  │
  ├─► Return success message
  │   └─► "Leave request submitted successfully"
  │
  └─► Redirect to /staff/leave-status

VIEW LEAVE STATUS
  │
  ├─► GET /staff/leave-status
  │   └─► StaffController@leaveStatus()
  │
  ▼
DISPLAY REQUESTS
  │
  ├─► Query LeaveRequest table
  │   └─► WHERE staff_id = {session_staff_id}
  │
  ├─► Group by Status
  │   ├─ Pending Requests (yellow)
  │   ├─ Approved Requests (green)
  │   └─ Rejected Requests (red)
  │
  ├─► Show for each request:
  │   ├─ Leave Type
  │   ├─ From Date - To Date
  │   ├─ Reason
  │   ├─ Status
  │   ├─ Admin notes (if rejected)
  │   └─ Proof file download button (if provided)
  │
  └─► Show notifications badge
      └─► Count of new approvals/rejections
```

---

## 4. ADMIN PORTAL FLOWS

### 4.1 ADMIN DASHBOARD

```
┌──────────────────────────────────────────────────────────────┐
│            ADMIN DASHBOARD (/admin_dashboard)               │
└──────────────────────────────────────────────────────────────┘

AUTHENTICATE
  │
  ├─► admin.auth middleware
  │   └─► Verify session['admin_id'] exists
  │
  ▼
COLLECT STATISTICS
  │
  ├─► GET Total Staff Count
  │   └─► COUNT(*) FROM staff
  │
  ├─► GET Present Today
  │   ├─► WHERE attendance_date = TODAY
  │   ├─► WHERE status IN ('present', 'late')
  │   └─► COUNT DISTINCT staff_id
  │
  ├─► GET Absent Today
  │   ├─► WHERE attendance_date = TODAY
  │   ├─► WHERE status = 'absent'
  │   └─► COUNT DISTINCT staff_id
  │
  ├─► GET On Leave Today
  │   ├─► Query approved leave requests
  │   ├─► WHERE from_date <= TODAY
  │   ├─► WHERE to_date >= TODAY
  │   └─► COUNT DISTINCT staff_id
  │
  ├─► GET Pending Leave Requests
  │   ├─► WHERE status = 'pending'
  │   └─► COUNT
  │
  ├─► GET Departments
  │   └─► COUNT(*) FROM departments
  │
  └─► GET Teams
      └─► COUNT(*) FROM teams

DISPLAY DASHBOARD
  │
  ├─► Show Welcome Card
  │   └─► Hello Admin {admin_name}!
  │
  ├─► Show Statistics Cards
  │   ├─► Total Staff (blue)
  │   ├─► Present Today (green)
  │   ├─► Absent Today (red)
  │   ├─► On Leave Today (yellow)
  │   └─► Pending Leave Requests (orange)
  │
  ├─► Show Charts
  │   ├─► Line chart (7-day trend)
  │   └─► Pie chart (status breakdown)
  │
  ├─► Show Quick Actions Menu
  │   ├─► Manage Staff
  │   ├─► View Attendance
  │   ├─► Review Leave Requests
  │   ├─► Manage Departments
  │   ├─► Manage Teams
  │   └─► View Reports
  │
  ├─► Show Notifications
  │   ├─► Recent leave request approvals/rejections
  │   └─► Recent staff activities
  │
  └─► Show Language Selector & Logout
```

### 4.2 MANAGE STAFF FLOW

```
┌──────────────────────────────────────────────────────────────┐
│            MANAGE STAFF (/admin/staff)                      │
└──────────────────────────────────────────────────────────────┘

DISPLAY STAFF LIST
  │
  ├─► GET /admin/staff
  │   └─► StaffManagementController@index()
  │
  ▼
QUERY STAFF DATA
  │
  ├─► GET all staff from database
  │   └─► With search/filter support
  │
  ├─► Optional Search
  │   ├─► By name (LIKE query)
  │   └─► By email (LIKE query)
  │
  └─► Show pagination (10-20 per page)

DISPLAY TABLE
  │
  ├─► Columns:
  │   ├─ Staff ID
  │   ├─ Name
  │   ├─ Email
  │   ├─ Department
  │   ├─ Team
  │   ├─ Actions
  │   └─► Edit | Delete | View Profile
  │
  └─► Show "Add New Staff" button

ADD NEW STAFF
  │
  ├─► GET /admin/staff/create
  │   └─► StaffManagementController@create()
  │
  ▼
DISPLAY FORM
  │
  ├─► Auto-generate Staff ID
  │   └─► Format: st{number} (e.g., st001, st002)
  │
  ├─► Input Fields:
  │   ├─ Staff Name (required)
  │   ├─ Email (required)
  │   ├─ Password (required)
  │   ├─ Department (dropdown)
  │   ├─ Team (dropdown)
  │   └─ Annual Leave Balance (default 20)
  │
  ├─► Department Dropdown
  │   └─► Populated from departments table
  │
  ├─► Team Dropdown
  │   ├─► Populated from teams table
  │   └─► Dynamically filtered by selected department
  │
  └─► Submit Button

CREATE STAFF
  │
  ├─► POST /admin/staff
  │   └─► StaffManagementController@store()
  │
  ▼
VALIDATION
  │
  ├─► Name is required
  │
  ├─► Email is valid and unique
  │   └─► Check for duplicate email
  │
  ├─► Password is strong (min 8 chars)
  │
  ├─► Department selected
  │
  ├─► Team selected
  │
  └─► Staff ID generation
      └─► Auto-generated if not provided

SAVE TO DATABASE
  │
  ├─► HASH password
  │   └─► Using bcrypt
  │
  ├─► CREATE Staff record
  │   ├─ staff_id = {generated_id}
  │   ├─ staff_name = {name}
  │   ├─ staff_email = {email}
  │   ├─ staff_password = {hashed}
  │   ├─ department_id = {dept_id}
  │   ├─ team_id = {team_id}
  │   └─ annual_leave_balance = 20
  │
  ├─► CREATE StaffProfile record
  │   └─► Empty profile for photo/details later
  │
  └─► SAVE to database

SUCCESS
  │
  ├─► Log staff creation
  │
  ├─► Return success message
  │   └─► "Staff {name} created successfully"
  │
  └─► Redirect to staff list

EDIT STAFF
  │
  ├─► GET /admin/staff/{id}/edit
  │   └─► StaffManagementController@edit()
  │
  ▼
DISPLAY FORM (pre-filled)
  │
  ├─► Current values shown
  │
  ├─► Same fields as create
  │
  └─► Can update all fields except Staff ID

SAVE CHANGES
  │
  ├─► PUT /admin/staff/{id}
  │   └─► StaffManagementController@update()
  │
  ▼
VALIDATION & SAVE
  │
  ├─► Validate fields
  │
  ├─► If password provided:
  │   └─► Hash and update
  │
  ├─► If email changed:
  │   └─► Check for uniqueness
  │
  └─► Update database

DELETE STAFF
  │
  ├─► DELETE /admin/staff/{id}
  │   └─► StaffManagementController@destroy()
  │
  ▼
CONFIRMATION
  │
  ├─► Show confirmation dialog
  │   └─► "Are you sure?"
  │
  ▼
DELETE OPERATION
  │
  ├─► Soft delete or hard delete per config
  │
  ├─► Log deletion
  │
  └─► Return success message
```

### 4.3 REVIEW LEAVE REQUESTS

```
┌──────────────────────────────────────────────────────────────┐
│       REVIEW LEAVE REQUESTS (/admin/leave-requests)        │
└──────────────────────────────────────────────────────────────┘

DISPLAY LEAVE REQUESTS
  │
  ├─► GET /admin/leave-requests
  │   └─► AdminController@leaveRequests()
  │
  ▼
QUERY DATA
  │
  ├─► Get all leave requests grouped by status
  │   ├─ Pending Requests (not yet reviewed)
  │   ├─ Approved Requests
  │   └─ Rejected Requests
  │
  ├─► For each request show:
  │   ├─ Staff Name
  │   ├─ Leave Type
  │   ├─ From Date - To Date
  │   ├─ Duration (calculated)
  │   ├─ Reason
  │   ├─ Proof File (if provided)
  │   ├─ Status
  │   ├─ Admin Notes
  │   └─ Action Buttons
  │
  └─► Show filter options
      ├─ By Status (Pending/Approved/Rejected)
      └─ By Leave Type

REVIEW REQUEST DETAILS
  │
  ├─► Click on request to expand
  │
  ├─► View:
  │   ├─ Full reason text
  │   ├─ Proof file (if provided)
  │   │   └─► Can preview PDF/Image or download
  │   ├─ Requested dates
  │   └─ Staff details
  │
  └─► Buttons: [Approve] [Reject]

APPROVE LEAVE REQUEST
  │
  ├─► POST /admin/leave/{id}/approve
  │   └─► AdminController@approveLease() [Note: typo in method name]
  │
  ▼
UPDATE DATABASE
  │
  ├─► SET status = 'approved'
  │
  ├─► SET approved_at = NOW()
  │
  ├─► SAVE to leave_requests table
  │
  └─► Create corresponding attendance records (optional)

SEND NOTIFICATION
  │
  ├─► Send email to staff
  │   └─► "Your leave request has been approved"
  │
  └─► Send in-app notification

SUCCESS
  │
  ├─► Mark request as approved
  │
  ├─► Show success message
  │
  └─► Refresh list

REJECT LEAVE REQUEST
  │
  ├─► Show dialog for rejection reason
  │
  ├─► Admin enters notes/reason
  │
  ├─► POST /admin/leave/{id}/reject
  │   └─► AdminController@rejectLeave()
  │
  ▼
UPDATE DATABASE
  │
  ├─► SET status = 'rejected'
  │
  ├─► SET rejected_at = NOW()
  │
  ├─► SET admin_notes = {reason}
  │
  └─► SAVE to database

SEND NOTIFICATION
  │
  ├─► Send email to staff
  │   └─► "Your leave request has been rejected"
  │   └─► Include admin notes
  │
  └─► Send in-app notification
```

### 4.4 MARK ATTENDANCE

```
┌──────────────────────────────────────────────────────────────┐
│     MARK ATTENDANCE (/admin/attendance)                     │
└──────────────────────────────────────────────────────────────┘

DISPLAY ATTENDANCE FORM
  │
  ├─► GET /admin/attendance
  │   └─► AdminAttendanceController@index()
  │
  ▼
QUERY STAFF LIST
  │
  ├─► GET all staff
  │
  └─► Display staff list with checkboxes/selection

MARK ATTENDANCE
  │
  ├─► For each staff, can set:
  │   ├─ Status (Present, Absent, Late, Leave, etc.)
  │   ├─ Check-in time (if applicable)
  │   ├─ Check-out time (if applicable)
  │   ├─ Remarks (optional)
  │   └─ Date (default today)
  │
  ├─► Bulk operations:
  │   ├─ Mark selected as Present
  │   ├─ Mark selected as Absent
  │   └─ Clear all selections
  │
  └─► Submit Button

SUBMIT ATTENDANCE
  │
  ├─► POST /admin/attendance/mark
  │   └─► AdminAttendanceController@mark()
  │
  ▼
SAVE ATTENDANCE RECORDS
  │
  ├─► For each staff marked:
  │   │
  │   ├─► Find or Create attendance record
  │   │   ├─ staff_id
  │   │   └─ attendance_date
  │   │
  │   ├─► UPDATE fields:
  │   │   ├─ status
  │   │   ├─ check_in_time
  │   │   ├─ check_out_time
  │   │   └─ remarks
  │   │
  │   └─► SAVE to database
  │
  └─► Log all changes

SUCCESS
  │
  ├─► Show success message
  │   └─► "{n} attendance records updated"
  │
  └─► Refresh page
```

### 4.5 ATTENDANCE REPORT

```
┌──────────────────────────────────────────────────────────────┐
│     ATTENDANCE REPORT (/admin/attendance/report)           │
└──────────────────────────────────────────────────────────────┘

DISPLAY REPORT FILTERS
  │
  ├─► GET /admin/attendance/report
  │   └─► AdminAttendanceController@report()
  │
  ▼
FILTER OPTIONS
  │
  ├─► Date Range Picker
  │   ├─ From Date
  │   └─ To Date
  │
  ├─► Staff Selection
  │   ├─ Single staff dropdown
  │   └─ OR All staff checkbox
  │
  ├─► Department Filter
  │   └─► Select or All
  │
  ├─► Status Filter
  │   ├─ Present
  │   ├─ Absent
  │   ├─ Late
  │   ├─ Leave
  │   └─ All
  │
  └─► Generate Report Button

GENERATE REPORT
  │
  ├─► Query attendance table with filters
  │   ├─► WHERE attendance_date BETWEEN from_date AND to_date
  │   ├─► WHERE staff_id = {selected} OR all
  │   ├─► WHERE status = {selected} OR all
  │   └─► ORDER BY staff_id, attendance_date
  │
  └─► Calculate statistics
      ├─ Total Present
      ├─ Total Absent
      ├─ Total Late
      └─ Percentage attendance

DISPLAY REPORT
  │
  ├─► Show statistics summary
  │
  ├─► Show attendance table
  │   ├─ Staff Name
  │   ├─ Staff ID
  │   ├─ Date
  │   ├─ Status
  │   ├─ Check-in
  │   ├─ Check-out
  │   └─ Remarks
  │
  └─► Export Options
      ├─ Download as PDF
      ├─ Download as CSV/Excel
      └─ Print
```

---

## 5. DATA FLOW BETWEEN COMPONENTS

### Database Tables & Relationships

```
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE SCHEMA                          │
└─────────────────────────────────────────────────────────────┘

STAFF (Primary Key: staff_id)
├─ staff_id (VARCHAR)
├─ staff_name
├─ staff_email (UNIQUE)
├─ staff_password (HASHED)
├─ department_id (FK)
├─ team_id (FK)
├─ annual_leave_balance
└─ timestamps

    │
    ├─ ONE-TO-ONE ──► STAFF_PROFILE
    │                ├─ phone
    │                ├─ address
    │                ├─ designation
    │                └─ hire_date
    │
    ├─ MANY-TO-ONE ──► DEPARTMENT
    │                ├─ name
    │                └─ manager_id (FK)
    │
    ├─ MANY-TO-ONE ──► TEAM
    │                ├─ name
    │                └─ team_lead_id (FK)
    │
    ├─ ONE-TO-MANY ──► ATTENDANCE
    │                ├─ attendance_date
    │                ├─ check_in_time
    │                ├─ check_out_time
    │                ├─ status
    │                └─ remarks
    │
    └─ ONE-TO-MANY ──► LEAVE_REQUEST
                    ├─ leave_type
                    ├─ from_date
                    ├─ to_date
                    ├─ reason
                    ├─ status
                    └─ proof_file

ADMIN (Primary Key: admin_id)
├─ admin_id
├─ admin_name
├─ admin_email (UNIQUE)
├─ admin_password (HASHED)
└─ timestamps

STAFF_SESSION (Session Tracking)
├─ id
├─ staff_id (FK)
├─ session_id
├─ ip_address
├─ user_agent
├─ logged_in_at
└─ last_activity_at
```

---

## 6. SYSTEM INTEGRATION POINTS

```
┌──────────────────────────────────────────────────────────────┐
│              SYSTEM INTEGRATION DIAGRAM                      │
└──────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│   WEB BROWSER                               │
│   ├─ Staff Portal (localhost:8000)         │
│   └─ Admin Portal (localhost:8000)         │
└────────────────┬────────────────────────────┘
                 │ HTTP Requests
                 ▼
┌─────────────────────────────────────────────┐
│   LARAVEL 11 APPLICATION                    │
│   ├─ Routes (web.php)                       │
│   ├─ Controllers                            │
│   │  ├─ AuthController                      │
│   │  ├─ AdminController                     │
│   │  ├─ StaffController                     │
│   │  ├─ AttendanceController                │
│   │  ├─ AdminAttendanceController           │
│   │  └─ StaffManagementController           │
│   ├─ Models (Eloquent ORM)                  │
│   │  ├─ Staff                               │
│   │  ├─ Admin                               │
│   │  ├─ Attendance                          │
│   │  ├─ LeaveRequest                        │
│   │  ├─ Department                          │
│   │  ├─ Team                                │
│   │  └─ StaffProfile                        │
│   ├─ Middleware                             │
│   │  ├─ staff.auth                          │
│   │  └─ admin.auth                          │
│   ├─ Views (Blade Templates)                │
│   │  ├─ login.blade.php                     │
│   │  ├─ admin_login.blade.php               │
│   │  ├─ staff_dashboard.blade.php           │
│   │  ├─ admin_dashboard.blade.php           │
│   │  └─ Other views                         │
│   └─ Session Management (File-based)        │
└────────────────┬────────────────────────────┘
                 │ SQL Queries
                 ▼
┌─────────────────────────────────────────────┐
│   MYSQL 8.0 DATABASE                        │
│   ├─ Table: staff                           │
│   ├─ Table: admin                           │
│   ├─ Table: attendance                      │
│   ├─ Table: leave_requests                  │
│   ├─ Table: departments                     │
│   ├─ Table: teams                           │
│   ├─ Table: staff_profile                   │
│   ├─ Table: staff_sessions                  │
│   └─ Indexes on: staff_id, attendance_date │
└─────────────────────────────────────────────┘

Optional Monitoring:
┌──────────────────────────────────────────────┐
│   PROMETHEUS (Port 9090)                     │
│   └─► Scrapes /metrics endpoint every 10s   │
└────────────────┬─────────────────────────────┘
                 │ PromQL Queries
                 ▼
┌──────────────────────────────────────────────┐
│   GRAFANA (Port 3000)                        │
│   ├─ Dashboard: Attendance Statistics        │
│   ├─ Metrics: Present, Absent, Late, etc    │
│   ├─ Auto-refresh: Every 10 seconds          │
│   └─ Charts: Pie, Line, Bar                  │
└──────────────────────────────────────────────┘
```

---

## 7. ERROR HANDLING & EDGE CASES

```
┌──────────────────────────────────────────────────────────────┐
│                  ERROR HANDLING FLOWS                        │
└──────────────────────────────────────────────────────────────┘

LOGIN ERRORS:
├─ Invalid email format → "Please enter a valid email"
├─ Staff ID not found → "Staff ID not found"
├─ Password incorrect → "Incorrect password"
├─ Admin not found → "Admin email not found"
└─ Session creation failed → "System error. Try again"

ATTENDANCE ERRORS:
├─ On approved leave → "Cannot check-in while on leave"
├─ Already marked absent → "Cannot check-in when marked absent"
├─ Check-out without check-in → "Must check-in first"
├─ Duplicate check-in → "Already checked in today"
└─ Invalid time format → "Invalid time format"

LEAVE REQUEST ERRORS:
├─ Invalid date range → "End date must be after start date"
├─ Date in past → "Cannot request leave in the past"
├─ Missing required fields → "{field} is required"
├─ Proof file too large → "File must be less than 5MB"
├─ Invalid file type → "Only PDF/JPG/PNG/DOC/DOCX allowed"
├─ Insufficient leave balance → "Not enough leave balance"
└─ Overlapping leaves → "You have leave on these dates"

STAFF MANAGEMENT ERRORS:
├─ Duplicate email → "Email already exists"
├─ Invalid email → "Please enter a valid email"
├─ Password too short → "Password must be at least 8 characters"
├─ Missing department → "Please select a department"
└─ Missing team → "Please select a team"

SESSION ERRORS:
├─ Session expired → Redirect to login
├─ Unauthorized access → 403 Forbidden
├─ Invalid route → 404 Not Found
└─ Server error → 500 Internal Server Error
```

---

## 8. SUMMARY OF KEY FLOWS

| Flow | Entry | Process | Exit |
|------|-------|---------|------|
| Staff Login | GET /login | Validate → DB Query → Password Check → Session | /staff_dashboard |
| Admin Login | GET /admin_login | Validate → DB Query → Password Check → Session | /admin_dashboard |
| Check-in | POST /attendance/check-in | Verify leave → Create record → Save | Success message |
| Check-out | POST /attendance/check-out | Verify checked in → Update → Save | Success message |
| Apply Leave | POST /staff/leave | Validate dates → Check balance → Save → Notify | /staff/leave-status |
| Approve Leave | POST /admin/leave/{id}/approve | Update status → Create attendance → Notify | Dashboard |
| Mark Attendance | POST /admin/attendance/mark | Bulk validate → Save records → Log | Dashboard |

---

**This flowchart represents the complete Staff Attendance Management System with all major flows, decision points, and data interactions.**
