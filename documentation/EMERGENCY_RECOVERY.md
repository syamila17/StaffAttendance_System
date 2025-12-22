# EMERGENCY FIX - System Down (500 Errors & Docker Issues)

## Status: CRITICAL

- ❌ Docker services not responding
- ❌ Laravel returning 500 errors
- ❌ ERR_EMPTY_RESPONSE from localhost

---

## Immediate Recovery Steps

### Step 1: Stop Everything
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System

# Stop Docker
docker-compose down

# Wait 5 seconds
Start-Sleep -Seconds 5
```

### Step 2: Clean Docker (Remove old containers)
```powershell
# Remove all stopped containers
docker container prune -f

# Remove unused volumes
docker volume prune -f
```

### Step 3: Rebuild and Start Fresh
```powershell
# Go to project directory
cd C:\Users\syami\Desktop\StaffAttendance_System

# Start services with rebuild
docker-compose up -d --build

# Wait 20 seconds for services to start
Start-Sleep -Seconds 20

# Check if running
docker ps
```

### Step 4: Check If Services Are Running
```powershell
# Should see 3 containers:
# - mysql_staff
# - phpmyadmin_staff
# - grafana_staff

docker ps
```

### Step 5: Setup Laravel Application
```powershell
cd staff_attendance

# Create .env if missing
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    echo "✅ .env created"
}

# Generate key
php artisan key:generate

# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate

# Link storage
php artisan storage:link
```

### Step 6: Start Laravel Server
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance

php artisan serve
```

### Step 7: Test the Application
```
Open in browser:
http://localhost:8000/login

Wait 10 seconds and test
```

---

## Diagnostic Checklist

Run these to identify problems:

### Check Docker Status
```powershell
# Containers running?
docker ps

# Container logs
docker-compose logs mysql
docker-compose logs grafana
docker-compose logs phpmyadmin
```

### Check Laravel
```powershell
cd staff_attendance

# Laravel version
php artisan -v

# Check config
php artisan config:list | grep -i database

# Test database
php artisan tinker
DB::connection()->getPdo()
exit
```

### Check Ports
```powershell
# Port 8000 (Laravel)
netstat -ano | findstr :8000

# Port 3307 (MySQL)
netstat -ano | findstr :3307

# Port 3000 (Grafana)
netstat -ano | findstr :3000

# Port 8081 (phpMyAdmin)
netstat -ano | findstr :8081
```

---

## If Docker Won't Start

### Issue 1: Port Already in Use
```powershell
# Find process using port
$process = Get-Process | Where-Object {$_.Description -like "*mysql*"}
Stop-Process -Id $process.Id -Force

# Or kill specific port
netstat -ano | findstr :3307
taskkill /PID [PID_NUMBER] /F
```

### Issue 2: Docker Daemon Not Running
```powershell
# Restart Docker Desktop
# Go to: Windows Menu → Docker Desktop
# Click Restart

# Or restart via command
net stop com.docker.service
Start-Sleep -Seconds 5
net start com.docker.service
```

### Issue 3: Disk Space Issue
```powershell
# Clean up Docker
docker system prune -a -f

# Check disk space
Get-Volume
```

---

## Complete System Reset (Nuclear Option)

```powershell
# Go to project
cd C:\Users\syami\Desktop\StaffAttendance_System

# Stop everything
docker-compose down --volumes

# Remove all Docker data
docker system prune -a -f

# Wait 10 seconds
Start-Sleep -Seconds 10

# Start fresh
docker-compose up -d --build

# Wait for services
Start-Sleep -Seconds 30

# Setup Laravel
cd staff_attendance
php artisan migrate --fresh
php artisan serve
```

---

## If PHP Artisan Not Working

### Check PHP Installation
```powershell
# Check PHP version
php --version

# Check PHP extensions
php -m | findstr -i pdo

# If no PDO, reinstall PHP with database extensions
```

### Check Composer
```powershell
# Update dependencies
composer update

# Install dependencies
composer install

# Dump autoload
composer dump-autoload
```

---

## Expected Output After Fix

### Docker Ps Should Show:
```
CONTAINER ID   IMAGE                PORTS              NAMES
abc123         mysql:8.0            3307->3306         mysql_staff
def456         phpmyadmin           8081->80           phpmyadmin_staff  
ghi789         grafana/grafana      3000->3000         grafana_staff
```

### Laravel Server Should Show:
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

### Login Should Work:
```
1. http://localhost:8000/login
2. Enter credentials
3. ✅ Redirect to dashboard (no 500 error)
```

---

## Troubleshooting Flow Chart

```
System Down?
    ↓
Are Docker containers running?
├─ NO → Run: docker-compose up -d
│
├─ YES → Check Laravel logs
    ├─ ERROR in log → Fix error
    │
    ├─ NO ERROR → Check database
        ├─ Can't connect? → Fix .env
        │
        └─ Connected → Check migrations
            └─ Tables missing? → php artisan migrate
```

---

## Quick Command Reference

```powershell
# Stop all services
docker-compose down

# Start all services
docker-compose up -d

# Restart services
docker-compose restart

# View logs
docker-compose logs -f

# Check status
docker ps

# Kill process on port
netstat -ano | findstr :8000
taskkill /PID [PID] /F

# Laravel setup
php artisan migrate
php artisan cache:clear
php artisan serve

# Database check
php artisan tinker
DB::connection()->getPdo()
exit

# View errors
Get-Content storage/logs/laravel.log -Tail 100
```

---

## Summary of Actions

1. ✅ Stop Docker and clean
2. ✅ Start Docker fresh
3. ✅ Setup Laravel (.env, migrations, caches)
4. ✅ Start Laravel server
5. ✅ Test login page

**Estimated Time:** 10-15 minutes

---

## If You Need Help

1. **Check logs:** `Get-Content storage/logs/laravel.log -Tail 100`
2. **Enable debug:** Edit `.env` → `APP_DEBUG=true`
3. **Test database:** `php artisan tinker` → `DB::connection()->getPdo()`
4. **Check ports:** `netstat -ano | findstr :8000`

---

**Document:** Emergency System Recovery Guide  
**Status:** Ready to execute  
**Priority:** CRITICAL - System is down
