# Staff Dashboard - Quick Reference Guide

## One-Page Summary

### What is the Staff Dashboard?
A real-time attendance tracking system with visual analytics showing:
- Today's attendance status (check-in, check-out, duration)
- Monthly attendance breakdown (pie chart via Grafana)
- Attendance statistics (Present, Absent, Late counts)
- 30-day attendance history

---

## Key Features

| Feature | Description | Tech Stack |
|---------|-------------|-----------|
| **Real-time Status** | Shows today's attendance with color codes | Laravel + PHP |
| **Pie Chart** | Visual breakdown of monthly attendance | Grafana + MySQL |
| **Month Selector** | Choose any month to view (12-month history) | JavaScript |
| **Auto-Refresh** | Updates every 30 seconds | JavaScript Timer |
| **Responsive Design** | Works on desktop, tablet, mobile | Tailwind CSS |
| **Dark Theme** | Professional dark UI with red accents | Tailwind CSS |

---

## System Requirements

### Minimum Specifications
| Component | Requirement |
|-----------|-------------|
| **RAM** | 4GB |
| **Disk Space** | 10GB |
| **CPU** | 2 cores |
| **OS** | Windows, Mac, Linux |

### Software Stack
- Laravel 10.x (PHP)
- MySQL 8.0
- Grafana 9.x
- Docker & Docker Compose
- Modern Web Browser

---

## Starting the System

### Quick Start (5 minutes)

```powershell
# Step 1: Navigate to project directory
cd C:\Users\syami\Desktop\StaffAttendance_System

# Step 2: Start Docker containers
docker-compose up -d

# Step 3: Navigate to Laravel app
cd staff_attendance

# Step 4: Start Laravel server
php artisan serve

# Step 5: Open in browser
# Dashboard: http://localhost:8000/staff/dashboard
# Grafana: http://localhost:3000
```

### Verify Everything is Running
```powershell
# Check containers
docker ps

# Expected output:
# grafana (port 3000)
# mysql (port 3307)
```

---

## Using the Dashboard

### Today's Attendance Card
```
┌────────────────────────────────────┐
│  TODAY'S ATTENDANCE                │
├────────────────────────────────────┤
│  Status    Check-in   Check-out    │
│  Present   09:15      17:45        │
│  Duration: 8h 30m                  │
└────────────────────────────────────┘

Status Colors:
🟢 Green = Present
🔴 Red = Absent
🟡 Yellow = Late
🟠 Orange = Emergency Leave
🔵 Blue = On Leave
🟣 Purple = Half Day
```

### Monthly Attendance Breakdown
```
Select Month:  [December 2025 ▼] [🔄 Refresh]

┌─────────────────────────────────┐
│                                 │
│       PIE CHART                 │
│                                 │
│  Present   ████ 60%             │
│  Absent    ██   20%             │
│  Late      ██   20%             │
│                                 │
└─────────────────────────────────┘

Last Updated: 14:23:45
(Auto-refreshes every 30 seconds)
```

### Statistics Cards
```
Total Present     Total Absent     Total Late     Quick Actions
    25                5                3          View Attendance
```

### Attendance History Table
```
Date          Status      Check-in  Check-out  Duration  Remarks
2025-12-10    Present     09:15     17:45      8h 30m    -
2025-12-09    Absent      -         -          -         -
2025-12-08    Late        09:45     17:30      7h 45m    Traffic
```

---

## Troubleshooting Quick Fixes

### Problem: Chart Not Showing

**Quick Fix Checklist:**
- [ ] Is Grafana running? `docker-compose up -d`
- [ ] Can you access Grafana? http://localhost:3000
- [ ] Does dashboard exist? Check Grafana admin panel
- [ ] Is MySQL connected? Check Grafana data sources
- [ ] Hard refresh browser? Ctrl+Shift+R

### Problem: Connection Error

**Quick Fix:**
```powershell
docker-compose restart grafana
docker-compose restart mysql
```

### Problem: No Data in Chart

**Quick Fix:**
```powershell
# Check if data exists
docker exec -it [mysql_container] mysql -u root -p
USE staffAttend_data;
SELECT COUNT(*) FROM attendance WHERE attendance_date >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Problem: Month Selector Doesn't Work

**Quick Fix:**
```
Press F12 (browser console)
Type: updateGrafanaChart()
Should see chart reload
```

---

## Important Files

| File | Purpose |
|------|---------|
| `staff_dashboard.blade.php` | Main dashboard template |
| `StaffController.php` | Provides dashboard data |
| `docker-compose.yml` | Container configuration |
| `IMPLEMENTATION_REPORT.md` | Full documentation |
| `TECHNICAL_TROUBLESHOOTING_GUIDE.md` | Detailed troubleshooting |

---

## URL Reference

| Service | URL | Login |
|---------|-----|-------|
| **Laravel Dashboard** | http://localhost:8000/staff/dashboard | Your staff account |
| **Grafana Admin** | http://localhost:3000 | admin / admin |
| **MySQL** | localhost:3307 | root / password |
| **phpMyAdmin** | http://localhost:8081 | root / password |

---

## Configuration Parameters

### Auto-Refresh Frequency
```
Current: 30 seconds
To change: Edit staff_dashboard.blade.php, line 413
Change: setInterval(..., 30000) → setInterval(..., 60000) for 60 seconds
```

### Chart Height
```
Current: 400px
To change: Edit staff_dashboard.blade.php, line 191
Change: style="height: 400px;" → style="height: 500px;"
```

### Grafana Dashboard URL
```
Current: http://localhost:3000/d-solo/adtx5zp/attendance-dashboard
Components:
- localhost:3000 (Grafana server)
- adtx5zp (Dashboard UID)
- attendance-dashboard (Dashboard name)
- panelId=1 (Pie chart panel ID)
```

---

## Database Schema (Key Tables)

### attendance Table
```sql
CREATE TABLE attendance (
  id INT PRIMARY KEY AUTO_INCREMENT,
  staff_id INT,
  attendance_date DATE,
  status ENUM('present', 'absent', 'late', 'el', 'on leave', 'half day'),
  check_in_time TIME,
  check_out_time TIME,
  remarks TEXT,
  el_reason TEXT,
  el_proof_file VARCHAR(255),
  el_proof_file_path VARCHAR(255),
  el_proof_uploaded_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Key Queries

**Count by status (used in pie chart):**
```sql
SELECT status, COUNT(*) as count 
FROM attendance 
WHERE attendance_date BETWEEN '2025-12-01' AND '2025-12-31'
GROUP BY status;
```

**Staff's attendance history:**
```sql
SELECT * FROM attendance 
WHERE staff_id = ? 
ORDER BY attendance_date DESC 
LIMIT 30;
```

---

## Common Tasks

### Add New Dashboard Panel

**Steps:**
1. Go to http://localhost:3000
2. Open attendance-dashboard
3. Click "Add" → "Add new panel"
4. Choose visualization type (Pie, Line, Bar, etc.)
5. Select MySQL data source
6. Write SQL query
7. Configure colors and legend
8. Save panel

### Change Theme/Colors

**In Grafana:**
1. Preferences → Dashboard → Theme (Light/Dark)
2. Panel → Visualization → Colors
3. Save dashboard

**In Laravel:**
1. Edit `staff_dashboard.blade.php`
2. Change Tailwind classes
3. Example: `from-red-800` → `from-blue-800`

### Export Data

**From attendance history table:**
1. Click "View Attendance"
2. Look for export button
3. Download as CSV/PDF

**From Grafana:**
1. Open dashboard
2. Click panel menu (⋮)
3. Select "Download CSV"
4. Save file

---

## Performance Tips

### Optimize Database
```sql
CREATE INDEX idx_attendance_date ON attendance(attendance_date);
CREATE INDEX idx_attendance_status ON attendance(status);
CREATE INDEX idx_staff_id ON attendance(staff_id);
```

### Reduce Auto-Refresh Load
```
Increase refresh interval:
30 seconds → 60 seconds (saves bandwidth)
Modify: setInterval(..., 60000)
```

### Clear Cache
```powershell
# Laravel cache
php artisan cache:clear

# View cache
php artisan view:clear

# Config cache
php artisan config:cache
```

---

## Development Notes

### Technology Choices

| Layer | Technology | Why Chosen |
|-------|-----------|-----------|
| **Frontend** | Blade + Tailwind | Server-side rendering, responsive design |
| **Backend** | Laravel | MVC framework, built-in features |
| **Database** | MySQL | Reliable, widely supported |
| **Charts** | Grafana | Professional, real-time, flexible |
| **Styling** | Tailwind CSS | Utility-first, dark theme support |

### Code Standards

- **PHP:** PSR-12 (Laravel conventions)
- **JavaScript:** ES6+ with vanilla JS
- **CSS:** Tailwind utility classes
- **Database:** Normalized schema, indexed queries

---

## Backup & Recovery

### Backup Database
```powershell
# Using Docker
docker exec [mysql_container] mysqldump -u root -p staffAttend_data > backup.sql

# Restore
docker exec -i [mysql_container] mysql -u root -p staffAttend_data < backup.sql
```

### Backup Grafana Dashboards
```
1. Go to http://localhost:3000
2. Dashboard menu → Save as JSON
3. Keep backup file safe
4. Can be imported later if needed
```

---

## Useful Commands Cheat Sheet

```powershell
# Docker
docker-compose up -d          # Start all services
docker-compose down           # Stop all services
docker-compose logs -f        # View live logs
docker ps                     # List running containers

# Laravel
php artisan serve             # Start Laravel server
php artisan migrate           # Run database migrations
php artisan cache:clear       # Clear application cache
php artisan tinker            # Interactive shell

# MySQL (via Docker)
docker exec -it [container] mysql -u root -p

# Browser Debugging
F12                           # Open DevTools
Ctrl+Shift+R                  # Hard refresh (clear cache)
Console tab                   # Run JavaScript commands
```

---

## Support Contacts

| Issue Category | Reference |
|---|---|
| **Dashboard Display** | See "Troubleshooting" section |
| **Grafana Setup** | TECHNICAL_TROUBLESHOOTING_GUIDE.md |
| **Database Issues** | IMPLEMENTATION_REPORT.md |
| **General Help** | IMPLEMENTATION_REPORT.md |

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Dec 10, 2025 | Initial release with pie chart and auto-refresh |
| 0.9 | Dec 9, 2025 | Bug fixes: route names, iframe issues |
| 0.8 | Dec 5, 2025 | Initial dashboard implementation |

---

**Last Updated:** December 10, 2025  
**Status:** Production Ready ✅  
**Need Help?** See IMPLEMENTATION_REPORT.md or TECHNICAL_TROUBLESHOOTING_GUIDE.md
