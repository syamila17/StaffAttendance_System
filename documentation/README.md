# Staff Attendance System

A comprehensive Laravel-based staff attendance management system with real-time Grafana dashboards.

## 📂 Project Structure

```
StaffAttendance_System/
├── documentation/          # All documentation files
│   ├── README.md          # Main documentation
│   ├── GRAFANA_SETUP_STEPS.md        # Grafana dashboard setup guide
│   ├── SETUP_GUIDE.md                # Project setup guide
│   ├── TROUBLESHOOTING_GRAFANA.md    # Common Grafana issues
│   ├── DATABASE_SCHEMA_ENHANCED.md   # Database schema reference
│   └── QUICK_COMMAND_REFERENCE.md    # Quick commands
├── staff_attendance/      # Laravel application
├── mysql_data/            # MySQL database files
├── grafana/               # Grafana configuration
├── docker-compose.yml     # Docker services config
└── prometheus.yml         # Prometheus metrics config
```

## 🚀 Quick Start

### 1. Start Services
```bash
docker-compose up -d
```

### 2. Set Up Laravel
```bash
cd staff_attendance
php artisan migrate
php artisan db:seed
```

### 3. Access the System
- **Laravel App**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8081 (root/root)
- **Grafana**: http://localhost:3000 (admin/admin)

## 📊 Features

### Admin Features
- Staff management (create, edit, delete)
- Attendance tracking
- Leave request management
- Real-time dashboards

### Staff Features
- Mark attendance (check-in/check-out)
- Request leave
- View attendance history
- Personal analytics

### Grafana Dashboards
- **Admin Dashboard**: Total staff, present today, on leave, absent
- **Staff Dashboard**: Personal attendance stats with trends

## 📖 Documentation

All documentation is in the `documentation/` folder:

- **Getting Started**: `SETUP_GUIDE.md`
- **Grafana Setup**: `GRAFANA_SETUP_STEPS.md`
- **Troubleshooting**: `TROUBLESHOOTING_GRAFANA.md`
- **Database Reference**: `DATABASE_SCHEMA_ENHANCED.md`
- **Quick Commands**: `QUICK_COMMAND_REFERENCE.md`

## 🔧 Technologies

- **Backend**: Laravel 12.37.0 with PHP 8.4.14
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS, Blade Templates
- **Monitoring**: Grafana, Prometheus
- **Export**: PDF generation with jsPDF

## 🐳 Docker Services

```yaml
MySQL:      localhost:3307 (database)
phpMyAdmin: localhost:8081 (management)
Grafana:    localhost:3000 (dashboards)
Laravel:    localhost:8000 (web app)
```

## 📝 Database Tables

- `staff` - Staff members
- `departments` - Departments
- `teams` - Teams
- `attendance` - Attendance records
- `leave_requests` - Leave applications
- `staff_profiles` - Staff additional info

## ✅ Maintenance

- Remove old `.md` files - cleanup complete ✓
- Documentation organized in `documentation/` folder ✓
- Duplicate files removed ✓

## 📞 Support

For issues or questions:
1. Check `TROUBLESHOOTING_GRAFANA.md`
2. Review `QUICK_COMMAND_REFERENCE.md`
3. Check phpMyAdmin for database issues

---

**Last Updated**: December 2, 2025
**Version**: 1.0

