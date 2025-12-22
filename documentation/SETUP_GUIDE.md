# 🚀 STEP-BY-STEP: How to Start the Server & Login

## What You're Seeing

```
❌ "Not Found" + Raw PHP Code showing
```

This means the Laravel development server is **NOT running**.

---

## The Solution

### Step 1: Open PowerShell or Command Prompt

Click **Windows Key** and type:
```
PowerShell
```

Press Enter to open.

---

### Step 2: Navigate to Project

Copy and paste this into PowerShell:
```powershell
cd "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"
```

Press Enter.

---

### Step 3: Start Laravel Server

Copy and paste this:
```powershell
php artisan serve
```

Press Enter.

**You should see:**
```
   INFO  Server running on [http://127.0.0.1:8000]

  Press Ctrl+C to quit
```

✅ If you see this, Laravel is running! Do NOT close this terminal.

---

### Step 4: Open Browser

In your browser:
1. Click address bar
2. Type: `http://localhost:8000/login`
3. Press Enter

**You should now see:**
- Login form with email and password fields
- NOT raw PHP code
- NOT "Not Found" error

---

### Step 5: Login

Enter these credentials:
```
Email: ahmad@utm.edu.my
Password: password123
```

Click **Login** button.

**You should see:**
- Redirect to dashboard
- Staff dashboard displays
- NO redirect loops

---

## ✅ Success Checklist

After step 3, check if you see:
- [ ] "Server running on [http://127.0.0.1:8000]"
- [ ] "Press Ctrl+C to quit"
- [ ] No errors in terminal
- [ ] Terminal is still open and responsive

After step 4, check if you see:
- [ ] Login form displays
- [ ] Email field visible
- [ ] Password field visible
- [ ] Login button visible

After step 5, you should see:
- [ ] Redirected to `/staff_dashboard`
- [ ] Dashboard content displays
- [ ] Welcome message visible

---

## 🐛 Troubleshooting

### Problem: "Command 'php' not found"
**Solution:** PHP is not in your system PATH
- Try: `C:\xampp\php\php artisan serve` (if using XAMPP)
- Or restart computer after PHP installation

### Problem: "Port 8000 already in use"
**Solution:** Another program is using port 8000
```powershell
php artisan serve --port=3000
# Then visit http://localhost:3000/login
```

### Problem: Server crashes/closes immediately
**Solution:** Clear caches first:
```powershell
php artisan cache:clear --force
php artisan view:clear --force
php artisan serve
```

### Problem: Still seeing raw PHP code
**Solution:** 
- Did you visit `http://localhost:8000/login`?
- Or did you visit the file directly? (wrong)
- Is the terminal showing "Server running"?
- Try refreshing (F5) or hard refresh (Ctrl+Shift+R)

---

## 📊 Visual Comparison

### ❌ WRONG (What you're seeing now)
```
URL: localhost:8000/login
Result: "Not Found" + Raw PHP code displayed
Reason: Laravel server not running
```

### ✅ CORRECT (What should happen)
```
URL: localhost:8000/login
Result: Login form displays
Reason: Laravel server is running and processing request
```

---

## 🎯 Important Notes

1. **Keep PowerShell open** - If you close it, server stops
2. **Use `http://` not `https://`** - Development server uses HTTP
3. **Use `localhost` not `127.0.0.1`** - Both work, but localhost is easier
4. **Try port 3000 if 8000 doesn't work** - Some systems have port conflicts
5. **Use INCOGNITO mode if issues** - Clears browser cache

---

## 🚀 Quick Command Reference

```powershell
# Navigate to project
cd "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"

# Start server on port 8000
php artisan serve

# Start server on port 3000 (if 8000 taken)
php artisan serve --port=3000

# Clear caches before starting
php artisan cache:clear --force
php artisan serve

# View recent logs (for debugging)
Get-Content -Tail 20 storage\logs\laravel.log
```

---

## ✅ Do This Right Now

1. Open PowerShell
2. Run: `cd "C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance"`
3. Run: `php artisan serve`
4. Wait for "Server running on" message
5. Open browser to `http://localhost:8000/login`
6. Tell me what you see!

**Don't close PowerShell - keep it open!**
