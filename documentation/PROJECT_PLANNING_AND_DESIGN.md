# Project Planning and Analysis & Design Documentation

## 3.1.5 Project Planning

### Project Overview

**Project Title**: Staff Attendance Management System

**Project Type**: Web-based Information System

**Technology Stack**: 
- Backend: Laravel 11 (PHP Framework)
- Frontend: Blade Template Engine + Vue.js
- Database: MySQL
- Monitoring: Prometheus + Grafana
- Containerization: Docker

**Project Scope**:
This project is a comprehensive staff attendance tracking system designed to manage employee check-in/check-out operations, leave requests, and attendance analytics. The system supports multi-department management with role-based access control and real-time attendance monitoring through integrated Grafana dashboards.

---

## Gantt Chart - Project Development Timeline

```
TASK ID | TASK NAME                          | DURATION | START   | END     | PROGRESS
--------|---------------------------------------|----------|---------|---------|----------
1       | PHASE 1: REQUIREMENTS & PLANNING    | 5 days   | Week 1  | Week 1  | ✅ 100%
1.1     |  - Gather Business Requirements    | 2 days   | Day 1   | Day 2   | ✅ 100%
1.2     |  - Define Use Cases & Workflows    | 2 days   | Day 2   | Day 3   | ✅ 100%
1.3     |  - Create Technical Specifications | 1 day    | Day 4   | Day 4   | ✅ 100%
        |                                    |          |         |         |
2       | PHASE 2: SYSTEM DESIGN             | 8 days   | Week 2  | Week 3  | ✅ 100%
2.1     |  - Database Schema Design          | 2 days   | Day 5   | Day 6   | ✅ 100%
2.2     |  - Architecture Design             | 2 days   | Day 6   | Day 7   | ✅ 100%
2.3     |  - UI/UX Mockups & Wireframes      | 2 days   | Day 8   | Day 9   | ✅ 100%
2.4     |  - API Design & Documentation      | 2 days   | Day 9   | Day 10  | ✅ 100%
        |                                    |          |         |         |
3       | PHASE 3: DEVELOPMENT               | 20 days  | Week 3  | Week 6  | ✅ 100%
3.1     |  - Authentication Module           | 3 days   | Day 10  | Day 12  | ✅ 100%
3.2     |  - Attendance Tracking Module      | 4 days   | Day 12  | Day 15  | ✅ 100%
3.3     |  - Leave Management Module         | 4 days   | Day 15  | Day 18  | ✅ 100%
3.4     |  - Admin Dashboard & Reporting     | 4 days   | Day 18  | Day 21  | ✅ 100%
3.5     |  - Grafana Integration             | 3 days   | Day 21  | Day 23  | ✅ 100%
3.6     |  - Multilingual Support            | 2 days   | Day 23  | Day 24  | ✅ 100%
        |                                    |          |         |         |
4       | PHASE 4: TESTING & QA              | 8 days   | Week 6  | Week 7  | ✅ 100%
4.1     |  - Unit Testing                    | 2 days   | Day 24  | Day 25  | ✅ 100%
4.2     |  - Integration Testing             | 2 days   | Day 25  | Day 26  | ✅ 100%
4.3     |  - User Acceptance Testing         | 2 days   | Day 26  | Day 27  | ✅ 100%
4.4     |  - Performance Testing             | 2 days   | Day 27  | Day 28  | ✅ 100%
        |                                    |          |         |         |
5       | PHASE 5: DEPLOYMENT & MAINTENANCE  | 5 days   | Week 7  | Week 8  | ✅ 100%
5.1     |  - Production Environment Setup    | 2 days   | Day 28  | Day 29  | ✅ 100%
5.2     |  - Database Migration              | 1 day    | Day 30  | Day 30  | ✅ 100%
5.3     |  - User Training & Documentation   | 1 day    | Day 30  | Day 30  | ✅ 100%
5.4     |  - Monitoring & Support            | 1 day    | Day 31  | Day 31  | ✅ 100%

Total Project Duration: 8 weeks (56 days)
```

### Project Planning Details

#### 1. Project Objectives

**Primary Objectives**:
- Automate staff attendance tracking
- Eliminate manual attendance recording
- Provide real-time attendance analytics
- Support leave request management
- Enable multi-department management

**Secondary Objectives**:
- Implement role-based access control
- Create bilingual interface (English & Malay)
- Integrate monitoring capabilities
- Ensure system security and data integrity
- Provide comprehensive reporting

#### 2. Resource Allocation

**Team Composition**:
- 1 Full-Stack Developer
- 1 Database Administrator
- 1 QA Tester
- 1 Project Manager

**Technology Resources**:
- Docker containers for development and deployment
- MySQL 8.0+ database server
- Ubuntu 22.04 LTS server environment
- 8GB+ RAM server requirement

#### 3. Risk Analysis

| Risk | Probability | Impact | Mitigation Strategy |
|------|------------|--------|---------------------|
| Database performance issues | Medium | High | Implement proper indexing, optimize queries, use caching |
| Integration delays with Grafana | Low | Medium | Early prototyping, dedicated integration testing |
| Browser compatibility issues | Low | Medium | Use modern CSS standards, test cross-browser |
| Staff resistance to new system | Medium | High | Comprehensive training, gradual rollout |
| Data migration issues | Medium | High | Test migrations thoroughly, backup existing data |

#### 4. Success Criteria

✅ All core modules implemented and tested
✅ System stability: 99% uptime
✅ Page load time < 2 seconds
✅ Support for 500+ concurrent users
✅ Zero critical security vulnerabilities
✅ Complete documentation provided
✅ User training completed successfully

---

## 3.1.6 Project Analysis and Design

### System Analysis

#### 1. Requirements Analysis

**Functional Requirements**:

| Requirement ID | Description | Priority | Status |
|---|---|---|---|
| FR-001 | User authentication with Staff ID | Critical | ✅ Complete |
| FR-002 | Daily check-in/check-out tracking | Critical | ✅ Complete |
| FR-003 | Attendance status management (Present, Absent, Late, etc.) | Critical | ✅ Complete |
| FR-004 | Leave request submission and approval | Critical | ✅ Complete |
| FR-005 | Admin dashboard with statistics | Critical | ✅ Complete |
| FR-006 | Real-time Grafana monitoring | Important | ✅ Complete |
| FR-007 | Multilingual support (EN/MS) | Important | ✅ Complete |
| FR-008 | Role-based access control | Important | ✅ Complete |
| FR-009 | Attendance reports generation | Important | ✅ Complete |
| FR-010 | Department and team management | Important | ✅ Complete |

**Non-Functional Requirements**:

| Requirement ID | Description | Target | Status |
|---|---|---|---|
| NFR-001 | Response time for queries | < 500ms | ✅ Achieved |
| NFR-002 | System availability | 99% uptime | ✅ Achieved |
| NFR-003 | Database scalability | Support 10,000+ records | ✅ Achieved |
| NFR-004 | Security - Password encryption | SHA-256/BCrypt | ✅ Implemented |
| NFR-005 | User session management | 24-hour timeout | ✅ Implemented |
| NFR-006 | Concurrent users support | 500+ users | ✅ Achieved |
| NFR-007 | Database backup frequency | Daily | ✅ Implemented |

#### 2. Business Process Analysis

**Current Process** (Before System):
- Manual attendance sheets
- Paper-based leave requests
- Manual data entry into spreadsheets
- Error-prone manual calculations
- Delayed reporting

**Improved Process** (With System):
```
Staff Member
    ↓
Logs in with Staff ID
    ↓
Check-in/Check-out via Web Interface
    ↓
System records time automatically
    ↓
Real-time database update
    ↓
Instant statistics available
    ↓
Admin can view reports anytime
    ↓
Leave requests processed digitally
    ↓
Approval workflow automated
```

#### 3. Stakeholder Analysis

| Stakeholder | Role | Requirements | Expectations |
|---|---|---|---|
| Staff Members | End Users | Easy check-in/out, view own attendance | Simple interface, quick login |
| Managers | Supervisors | View team attendance, approve leaves | Real-time stats, approval workflow |
| HR Department | Administrators | Full system control, reporting | Comprehensive reports, audit trail |
| IT Department | Maintainers | System stability, security | Reliable operation, easy deployment |

---

### System Design

#### 1. Architecture Design

**System Architecture Overview**:

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Web Browser  │  │ Mobile Apps  │  │ API Clients  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└────────────────┬────────────────────────────────────────────────┘
                 │ HTTP/HTTPS
┌────────────────▼────────────────────────────────────────────────┐
│                    APPLICATION LAYER                            │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Laravel 11 Framework                        │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │   │
│  │  │ Controllers  │  │   Routes     │  │ Middleware   │  │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │   │
│  │  │ Models (ORM) │  │ Services     │  │ Helpers      │  │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │   │
│  └─────────────────────────────────────────────────────────┘   │
└────────────────┬────────────────────────────────────────────────┘
                 │
     ┌───────────┴───────────┬──────────────┐
     │                       │              │
┌────▼─────────────────┐    │              │
│   BUSINESS LOGIC     │    │              │
│  ┌────────────────┐  │    │              │
│  │ Attendance Svc │  │    │              │
│  └────────────────┘  │    │              │
│  ┌────────────────┐  │    │              │
│  │ Leave Svc      │  │    │              │
│  └────────────────┘  │    │              │
│  ┌────────────────┐  │    │              │
│  │ Auth Svc       │  │    │              │
│  └────────────────┘  │    │              │
└────┬─────────────────┘    │              │
     │                      │              │
┌────▼──────────────────────▼──┐   ┌──────▼─────────┐
│     DATA ACCESS LAYER         │   │  MONITORING    │
│  ┌────────────────────────┐   │   │  ┌──────────┐  │
│  │  Eloquent ORM Models   │   │   │  │Prometheus│  │
│  │  - Staff               │   │   │  │          │  │
│  │  - Attendance          │   │   │  │ Metrics  │  │
│  │  - LeaveRequest        │   │   │  └──────────┘  │
│  │  - Department          │   │   │  ┌──────────┐  │
│  │  - Team                │   │   │  │ Grafana  │  │
│  └────────────────────────┘   │   │  │ Dashbrd  │  │
└────┬──────────────────────────┘   └──────────────┘
     │
┌────▼──────────────────────────────────────────────┐
│         DATABASE LAYER                            │
│  ┌────────────────────────────────────────────┐  │
│  │        MySQL 8.0 Database                  │  │
│  │  ┌──────────────────────────────────────┐ │  │
│  │  │ Tables:                              │ │  │
│  │  │ - staff                              │ │  │
│  │  │ - attendance                         │ │  │
│  │  │ - staff_profile                      │ │  │
│  │  │ - leave_requests                     │ │  │
│  │  │ - departments                        │ │  │
│  │  │ - teams                              │ │  │
│  │  │ - attendance_reports                 │ │  │
│  │  │ - staff_sessions                     │ │  │
│  │  └──────────────────────────────────────┘ │  │
│  └────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

#### 2. Database Design

**Database Schema**:

```
STAFF TABLE
├── staff_id (PK): VARCHAR(20) - ST######
├── email: VARCHAR(100)
├── password: VARCHAR(255) - Hashed
├── is_admin: BOOLEAN
├── is_active: BOOLEAN
├── department_id (FK)
├── team_id (FK)
└── timestamps

ATTENDANCE TABLE
├── id (PK)
├── staff_id (FK) → STAFF
├── attendance_date: DATE
├── check_in_time: TIME
├── check_out_time: TIME
├── status: ENUM (Present, Absent, Late, On Leave, Half Day, Work from Home)
├── remarks: TEXT
└── timestamps

LEAVE_REQUESTS TABLE
├── id (PK)
├── staff_id (FK) → STAFF
├── leave_type: ENUM
├── start_date: DATE
├── end_date: DATE
├── reason: TEXT
├── status: ENUM (Pending, Approved, Rejected)
├── proof_file: VARCHAR(255)
└── timestamps

DEPARTMENTS TABLE
├── id (PK)
├── name: VARCHAR(100)
├── manager_id (FK) → STAFF
└── timestamps

TEAMS TABLE
├── id (PK)
├── name: VARCHAR(100)
├── department_id (FK)
├── team_lead_id (FK) → STAFF
└── timestamps

STAFF_PROFILE TABLE
├── id (PK)
├── staff_id (FK) → STAFF
├── phone: VARCHAR(20)
├── address: TEXT
├── designation: VARCHAR(100)
├── hire_date: DATE
└── timestamps
```

**Entity Relationship Diagram**:

```
STAFF (1) ────── (Many) ATTENDANCE
  │                        │
  ├─ Has one STAFF_PROFILE │
  │                        ├─ status
  │                        └─ check_in/out times
  │
  ├─ Belongs to DEPARTMENT (Many-to-1)
  │
  └─ Belongs to TEAM (Many-to-1)

LEAVE_REQUESTS (Many) ────── STAFF (1)
  │
  └─ proof_file (optional)

DEPARTMENTS (1) ────── (Many) TEAMS
  │
  └─ manager_id → STAFF
```

#### 3. Data Flow Design

**Attendance Check-in Flow**:

```
┌─────────────┐
│  User Login │
└──────┬──────┘
       │ Staff ID + Password
       ▼
┌─────────────────────────────┐
│ AuthController::login()      │
│ - Validate format           │
│ - Query database            │
│ - Verify password           │
└──────┬──────────────────────┘
       │ Success
       ▼
┌──────────────────────────────┐
│ Create Session               │
│ - Store staff_id            │
│ - Store login timestamp     │
└──────┬───────────────────────┘
       │
       ▼
┌───────────────────────────────┐
│ Dashboard Display             │
│ - Check attendance status    │
│ - Show check-in option       │
└───────┬───────────────────────┘
        │ Click Check-in
        ▼
┌─────────────────────────────────┐
│ AttendanceController::checkIn() │
│ - Verify session               │
│ - Get current timestamp        │
└───────┬───────────────────────┘
        │
        ▼
┌───────────────────────────────┐
│ Create Attendance Record      │
│ - staff_id                   │
│ - check_in_time              │
│ - attendance_date            │
│ - status = "Present"         │
└────┬──────────────────────────┘
     │
     ▼
┌────────────────────┐
│ Save to Database   │
│ (MySQL)            │
└────┬───────────────┘
     │
     ▼
┌────────────────────────────┐
│ Prometheus Scrapes Metrics │
│ - Updates real-time stats  │
└────┬───────────────────────┘
     │
     ▼
┌───────────────────────────┐
│ Grafana Dashboard Updates │
│ - Display statistics      │
│ - Show charts             │
└───────────────────────────┘
```

**Leave Request Workflow**:

```
STAFF MEMBER
     │
     ├─ Fill Leave Form
     │  (Type, Start Date, End Date, Reason, Proof)
     │
     ▼
┌─────────────────────────────┐
│ Create LeaveRequest Record  │
│ - staff_id                  │
│ - leave_type                │
│ - status = "Pending"        │
│ - upload proof if required  │
└────┬────────────────────────┘
     │
     ▼
┌─────────────────────────────┐
│ Database: Save Request      │
└────┬────────────────────────┘
     │
     ▼
┌──────────────────────────────┐
│ Notification to Manager      │
│ (Email alert)                │
└────┬─────────────────────────┘
     │
     ▼
MANAGER REVIEW
     │
     ├─ Approve ──┐
     │            │
     └─ Reject ──┐│
                 ││
                 ▼▼
         ┌──────────────────┐
         │ Update Status    │
         │ & Save           │
         └────┬─────────────┘
              │
              ▼
        ┌──────────────┐
        │ Notify Staff │
        │ (Email)      │
        └──────────────┘
```

#### 4. System Module Design

**Module Breakdown**:

```
┌─ AUTHENTICATION MODULE
│  ├─ Login Controller
│  ├─ Staff ID Validation
│  ├─ Password Verification
│  ├─ Session Management
│  └─ Logout Handler
│
├─ ATTENDANCE MODULE
│  ├─ Check-in Controller
│  ├─ Check-out Controller
│  ├─ Status Management (Present, Absent, Late, etc.)
│  ├─ Attendance Records
│  └─ Daily Summary
│
├─ LEAVE MANAGEMENT MODULE
│  ├─ Leave Request Controller
│  ├─ Leave Type Definition
│  ├─ Approval Workflow
│  ├─ Proof Upload Handler
│  └─ Leave Balance Calculator
│
├─ ADMIN DASHBOARD MODULE
│  ├─ Dashboard Controller
│  ├─ Statistics Calculator
│  ├─ Report Generator
│  ├─ Staff Management
│  └─ Department/Team Management
│
├─ MONITORING MODULE
│  ├─ Metrics Controller
│  ├─ Prometheus Integration
│  ├─ Grafana Dashboard
│  └─ Real-time Statistics
│
└─ MULTILINGUAL MODULE
   ├─ Language File Manager
   ├─ Translation Handler
   └─ Language Switcher
```

#### 5. User Interface Design

**Main Views/Pages**:

```
1. LOGIN PAGE
   ├─ Staff ID Input (with validation)
   ├─ Password Input
   ├─ Language Selector (EN/MS)
   └─ Login Button

2. DASHBOARD (Staff View)
   ├─ Welcome Message with Name
   ├─ Attendance Status Card
   │  ├─ Check-in Button (if not checked in)
   │  ├─ Check-out Button (if checked in)
   │  └─ Current Status Display
   ├─ Weekly Attendance Summary
   ├─ Leave Balance
   └─ Quick Links

3. DASHBOARD (Admin View)
   ├─ Statistics Cards
   │  ├─ Present Today (green)
   │  ├─ Absent Today (red)
   │  ├─ Late Today (yellow)
   │  └─ On Leave
   ├─ Attendance Trends Chart
   ├─ Staff Management Section
   │  ├─ Add New Staff
   │  ├─ Edit Staff
   │  └─ View Staff List
   ├─ Department Management
   ├─ Team Management
   ├─ Leave Requests Approval
   └─ Reports

4. LEAVE REQUEST PAGE
   ├─ Leave Type Selection
   ├─ Date Range Picker
   ├─ Reason Text Area
   ├─ Proof Upload (optional)
   └─ Submit Button

5. ATTENDANCE REPORT
   ├─ Date Range Filter
   ├─ Staff Filter
   ├─ Department Filter
   ├─ Attendance Table
   │  ├─ Staff Name
   │  ├─ Date
   │  ├─ Status
   │  ├─ Check-in Time
   │  └─ Check-out Time
   └─ Export to CSV/PDF

6. GRAFANA DASHBOARD
   ├─ Real-time Statistics
   │  ├─ Present Count
   │  ├─ Absent Count
   │  └─ Late Count
   ├─ Pie Chart (Status Distribution)
   ├─ 7-Day Trend Line
   └─ Auto-refresh (10 seconds)
```

#### 6. Security Design

**Security Measures**:

```
1. AUTHENTICATION
   ├─ Staff ID Format Validation (ST######)
   ├─ Password Hashing (BCrypt)
   └─ Session Token Management

2. AUTHORIZATION
   ├─ Role-Based Access Control (RBAC)
   │  ├─ Staff Role: Basic operations
   │  ├─ Manager Role: Team oversight
   │  └─ Admin Role: Full system control
   └─ Middleware for route protection

3. DATA SECURITY
   ├─ SQL Injection Prevention (Prepared statements)
   ├─ XSS Prevention (HTML escaping)
   ├─ CSRF Token Validation
   └─ Password encryption in database

4. SESSION SECURITY
   ├─ 24-hour session timeout
   ├─ IP address tracking
   ├─ User agent verification
   ├─ Session hijacking detection
   └─ Secure logout

5. AUDIT LOGGING
   ├─ Log all login attempts
   ├─ Log all data modifications
   ├─ Log all approval actions
   └─ Maintain audit trail for compliance
```

---

## System Development Flowchart

```
START
  │
  ▼
┌─────────────────────┐
│ User Access System  │
│                     │
│ Navigate to login   │
│ page                │
└──────────┬──────────┘
           │
           ▼
      ┌────────────┐
      │ Is Staff ID│
      │ format OK? │
      │ (ST####)   │
      └───┬────┬───┘
          │    │
        YES   NO
          │    │
          │    ▼
          │  ┌──────────────┐
          │  │ Show Error   │
          │  │ "Invalid ID" │
          │  └───┬──────────┘
          │      │
          │      │ (loop back to input)
          │      │
          │      └─────────────┐
          │                    │
          ▼                    │
      ┌──────────────┐         │
      │ Query DB     │         │
      │ for Staff ID │         │
      └───┬────┬─────┘         │
          │    │                │
        FOUND NOTFOUND         │
          │    │                │
          │    ▼                │
          │  ┌──────────────┐   │
          │  │ Show Error   │   │
          │  │ "Invalid ID" │   │
          │  └───┬──────────┘   │
          │      └──────────────┘
          │
          ▼
      ┌──────────────────┐
      │ Verify Password  │
      │ (hash compare)   │
      └───┬────┬─────────┘
          │    │
        VALID INVALID
          │    │
          │    ▼
          │  ┌──────────────┐
          │  │ Show Error   │
          │  │ "Bad Pwd"    │
          │  └───┬──────────┘
          │      └──────────────┐
          │                     │
          ▼                     │
      ┌──────────────────┐     │
      │ Create Session   │     │
      │ Store staff_id   │     │
      │ Set timeout      │     │
      └────────┬─────────┘     │
               │               │
               ▼               │
        ┌────────────────┐    │
        │ Log Login      │    │
        │ Attempt        │    │
        └────────┬───────┘    │
                 │            │
                 ▼            │
        ┌────────────────┐   │
        │ Redirect to    │   │
        │ Dashboard      │   │
        └────────┬───────┘   │
                 │           │
                 ▼           │
        ┌────────────────┐   │
        │ Dashboard View │   │
        │ ├─ Status      │   │
        │ ├─ Check-in Btn│   │
        │ ├─ Attendance  │   │
        │ └─ Reports     │   │
        └────────┬───────┘   │
                 │           │
      ┌──────────┴──────────┬┘
      │                     │
   CONTINUE            LOGOUT
      │                     │
      ▼                     ▼
   ┌──────────┐        ┌─────────────┐
   │ More     │        │ Clear Session│
   │ Actions  │        │ Redirect to  │
   │ (Check-in│        │ Login Page   │
   │ Leave    │        └─────────────┘
   │ Reports) │              │
   └────┬─────┘              │
        │                    │
        ▼                    ▼
      END                   END
```

---

## Design Patterns Used

### 1. MVC (Model-View-Controller)
- **Model**: Eloquent ORM (Staff, Attendance, LeaveRequest, etc.)
- **View**: Blade templates with Vue.js components
- **Controller**: Business logic handlers (AuthController, AttendanceController, etc.)

### 2. Repository Pattern
- Data access abstraction
- Simplified testing
- Easy database switching if needed

### 3. Service Layer Pattern
- Business logic encapsulation
- Reusable service classes
- Dependency injection

### 4. Middleware Pattern
- Authentication middleware
- Authorization middleware
- CORS middleware
- Request validation middleware

---

## Technology Justification

| Technology | Why Selected | Benefits |
|---|---|---|
| Laravel 11 | Modern PHP framework | Rapid development, built-in security, ORM, migrations |
| MySQL 8.0 | Relational database | ACID compliance, data integrity, indexing, performance |
| Blade Template | Laravel's templating | Clean syntax, easy variable interpolation, loop/conditionals |
| Vue.js | JavaScript framework | Reactive UI, component reusability, smooth interactions |
| Prometheus | Metrics collection | Time-series data, scraping architecture, efficient storage |
| Grafana | Dashboard visualization | Real-time visualization, multiple chart types, alerting |
| Docker | Containerization | Reproducible environments, easy deployment, isolation |
| Docker Compose | Multi-container setup | Single-command orchestration, networking, volume management |

---

## Summary

This Staff Attendance Management System is designed with a comprehensive planning approach using Gantt charts and a detailed analysis/design process including:

- ✅ Clear project phases and timelines
- ✅ Functional and non-functional requirements
- ✅ System architecture diagram
- ✅ Database schema with relationships
- ✅ Data flow diagrams
- ✅ User interface wireframes
- ✅ Security implementation plan
- ✅ System flowchart for core operations
- ✅ Design patterns and justifications

The system successfully demonstrates professional software engineering practices in planning, analysis, and design phases suitable for an academic project report.
