# QUICK FIX - 10 Minutes to Working System

## Follow These Steps EXACTLY in Order

### STEP 1: Open PowerShell and Stop Everything (1 minute)
```powershell
cd C:\Users\syami\Desktop\StaffAttendance_System
docker-compose down
```

**Wait for command to complete**

---

### STEP 2: Clean Docker (1 minute)
```powershell
docker container prune -f
docker volume prune -f
```

---

### STEP 3: Start Services Fresh (3 minutes)
```powershell
docker-compose up -d --build
```

**Wait 20-30 seconds for services to start**

Verify with:
```powershell
docker ps
```

**Expected: 3 containers running**

---

### STEP 4: Setup Laravel (3 minutes)
```powershell
cd staff_attendance

# Create .env
if (!(Test-Path .env)) { Copy-Item .env.example .env }

# Generate key
php artisan key:generate

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link
```

---

### STEP 5: Start Laravel Server (1 minute)
```powershell
php artisan serve
```

**Should see:**
```
INFO  Server running on [http://127.0.0.1:8000].
```

---

### STEP 6: Test (1 minute)
Open browser and visit:
```
http://localhost:8000/login
```

**Should see login page with no errors**

---

## If It Still Doesn't Work

### Check Docker Status:
```powershell
docker ps
# Should show 3 containers (mysql, phpmyadmin, grafana)
```

### Check Laravel Logs:
```powershell
cd staff_attendance
Get-Content storage/logs/laravel.log -Tail 50
```

### Test Database:
```powershell
php artisan tinker
DB::connection()->getPdo()
exit
```

### Enable Debug Mode:
```powershell
# Edit .env
# Find: APP_DEBUG=false
# Change to: APP_DEBUG=true

# Restart server:
# Ctrl+C to stop
# php artisan serve to start again
```

---

## Common Issues & Quick Fixes

| Issue | Solution |
|-------|----------|
| `ERR_EMPTY_RESPONSE` | Docker not running: `docker-compose up -d` |
| `500 Server Error` | Check logs: `Get-Content storage/logs/laravel.log -Tail 50` |
| `Connection refused` | Wait 30 seconds for MySQL to start |
| `No such table` | Run: `php artisan migrate` |
| `Port already in use` | `netstat -ano \| findstr :8000` then `taskkill /PID [PID] /F` |

---

## Success Indicators

✅ `docker ps` shows 3 containers  
✅ `php artisan serve` runs without errors  
✅ Browser: http://localhost:8000/login loads login page  
✅ Login page has no red error messages  
✅ Can enter credentials and submit

---

## Timeline

- **Minute 0-1:** Stop Docker
- **Minute 1-2:** Clean up
- **Minute 2-5:** Start fresh services
- **Minute 5-8:** Setup Laravel
- **Minute 8-9:** Start server
- **Minute 9-10:** Test in browser

**Total time: 10 minutes**

---

**IMPORTANT:** Follow steps in exact order. Don't skip any steps.

If step 6 (test) shows no errors, the system is working again! ✅
