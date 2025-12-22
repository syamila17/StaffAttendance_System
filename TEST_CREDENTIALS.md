# Staff ID Authentication - Test Credentials

After running the database conversion, use these credentials to test the new Staff ID login system:

## Test Login Credentials

| Staff Name | Staff ID | Password | Notes |
|------------|----------|----------|-------|
| Ahmed Ali | ST110110 | (use existing password) | First staff member |
| Fatima Khan | ST110111 | (use existing password) | |
| Hassan Omar | ST110112 | (use existing password) | |
| Layla Noor | ST110113 | (use existing password) | |
| Mariam Hassan | ST110114 | (use existing password) | |
| Noor Ahmed | ST110115 | (use existing password) | Last staff member |

## How to Get Original Passwords

Since staff_ids have been converted, passwords remain unchanged. If you need to reset:

1. **phpMyAdmin** (http://localhost:8081):
   - Database: `staffAttend_data`
   - Table: `staff`
   - View `staff_password` column (contains hashed passwords)

2. **From Previous Records**:
   - Check system logs
   - Ask staff members for their passwords
   - Or reset via password reset feature

## Test Steps

### Basic Login Test
1. Open: **http://localhost:8000/login**
2. Enter:
   - **Staff ID**: ST110110
   - **Password**: [correct password for Ahmed Ali]
3. Click **Login**
4. Expected: Redirect to staff dashboard

### Dashboard Verification
After login, verify:
- ✅ Dashboard displays correctly
- ✅ Staff name shows in header
- ✅ Monthly attendance section works
- ✅ Pie chart displays (auto-refreshes every 10 seconds)
- ✅ Language switcher works (ENG/BM)
- ✅ Refresh button works in Monthly Attendance

### Multiple User Test
Try logging in as different staff:
- Logout current user: Click logout button
- Login as ST110111 (Fatima Khan)
- Verify dashboard for different user

### Bilingual Test
1. On login page, click **BM** button
2. Verify form shows in Malay:
   - "ID Staff" (instead of "Staff ID")
   - "Kata Laluan" (instead of "Password")
   - "Log Masuk" (instead of "Login")

## Session Verification

Check active sessions in database:

```sql
-- View current sessions
SELECT * FROM staff_sessions ORDER BY logged_in_at DESC;

-- Should show:
-- staff_id: ST110110 (format)
-- ip_address: your IP
-- user_agent: your browser
-- logged_in_at: current timestamp
```

## Password Reset Test (Optional)

If you need to test password reset:
1. Go to login page
2. Click "Forgot Password" link (if available)
3. Enter email address
4. Follow reset instructions
5. Set new password
6. Login with new password + Staff ID

## Troubleshooting Test Login

### "Staff ID not found in the system"
- [ ] Did you run the conversion script?
- [ ] Check: Visit http://localhost:8000/debug-staff
- [ ] Verify: Staff IDs show as ST110110, ST110111, etc.

### "Incorrect password"
- [ ] Try different password
- [ ] Check password hasn't been changed
- [ ] Verify hash method compatible (password_verify)

### Dashboard Won't Load
- [ ] Clear browser cache: Ctrl+Shift+R
- [ ] Check Laravel logs: `storage/logs/laravel.log`
- [ ] Verify session was created in database

### Language Toggle Not Working
- [ ] Click ENG or BM button
- [ ] Check: ?lang=en or ?lang=ms in URL
- [ ] Clear cache and try again

## Performance Considerations

- **First Login**: May take a few seconds (session creation)
- **Multiple Users**: Can handle concurrent sessions
- **Chart Auto-Refresh**: Every 10 seconds (normal)
- **Database**: Queries optimized with indexes on staff_id

## Success Indicators

✅ Successfully logged in as Staff ID (not email)
✅ Dashboard displays correctly
✅ Session created in staff_sessions table
✅ Logout removes session
✅ Can login as different staff members
✅ Language switching works
✅ All features work (pie chart, attendance, etc.)

---

**Ready to Test?** 
1. Convert database IDs (see QUICK_SETUP_GUIDE.md)
2. Use credentials above
3. Test login at http://localhost:8000/login
