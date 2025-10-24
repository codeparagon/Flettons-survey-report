# Test Credentials - Survey System

## Login Accounts

All passwords are: `password`

### Super Admin
```
Email: admin@flettons.com
Password: password
Name: Super Admin
Access: Full system access
```

**Can Access:**
- Admin Dashboard: `/admin/dashboard`
- All Surveys: `/admin/surveys`
- User Management: `/admin/users`
- Edit/Assign Surveys
- All Admin Functions

---

### Surveyor
```
Email: surveyor@flettons.com
Password: password
Name: John Surveyor
Access: Assigned surveys only
```

**Can Access:**
- Surveyor Dashboard: `/surveyor/dashboard`
- My Surveys: `/surveyor/surveys` (only assigned to them)
- Update Survey Status
- NewDesign Survey Pages

**Cannot Access:**
- Other surveyors' surveys
- Admin panel
- Unassigned surveys

---

### Client
```
Email: client@flettons.com
Password: password
Name: Jane Client
Access: Own surveys only (read-only)
```

**Can Access:**
- Client Dashboard: `/client/dashboard`
- My Surveys: `/client/surveys` (read-only)
- Survey Reports (when available)

**Cannot Access:**
- Admin functions
- Other clients' surveys
- Survey editing

---

## Quick Test Flow

### 1. Test as Admin
```bash
# Login
URL: http://localhost:8000/login
Email: admin@flettons.com
Password: password

# After Login
→ Redirected to: /admin/dashboard
→ Click "Surveys" to see all surveys
→ Click survey #3 (Michael Brown - Pending)
→ Click "Edit"
→ Assign to "John Surveyor"
→ Set status to "assigned"
→ Save
```

### 2. Test as Surveyor
```bash
# Logout and Login
URL: http://localhost:8000/login
Email: surveyor@flettons.com
Password: password

# After Login
→ Redirected to: /surveyor/dashboard
→ Click "Surveys" to see YOUR assigned surveys only
→ Click a survey to view details
→ Update status to "In Progress"

# Access Survey Interface
→ New Tab: http://localhost:8000/newdesign/pages/new-survey.html
→ Click "Roofs" category
→ Fill out form
```

### 3. Test as Client
```bash
# Logout and Login
URL: http://localhost:8000/login
Email: client@flettons.com
Password: password

# After Login
→ Redirected to: /client/dashboard
→ View your surveys (read-only)
→ Check survey status
```

---

## Database Test Queries

### View All Users
```sql
SELECT id, name, email, role_id, status FROM users;
```

Expected Result:
```
ID  Name              Email                      Role ID  Status
1   Super Admin       admin@flettons.com         1        active
2   John Surveyor     surveyor@flettons.com      2        active
3   Jane Client       client@flettons.com        3        active
```

### View All Surveys
```sql
SELECT 
    id,
    CONCAT(first_name, ' ', last_name) as client,
    status,
    surveyor_id
FROM surveys;
```

Expected Result:
```
ID  Client              Status         Surveyor ID
1   John Smith         assigned       2
2   Sarah Johnson      in_progress    2
3   Michael Brown      pending        NULL (or 2 if assigned)
4   Emma Williams      completed      2
5   David Taylor       pending        NULL
```

### View Surveyor's Assignments
```sql
SELECT 
    s.id,
    CONCAT(s.first_name, ' ', s.last_name) as client,
    s.status,
    u.name as surveyor
FROM surveys s
LEFT JOIN users u ON s.surveyor_id = u.id
WHERE s.surveyor_id = 2;
```

---

## Security Tests

### ✅ Test 1: Surveyor Isolation
```
1. Login as surveyor@flettons.com
2. Go to /surveyor/surveys
3. You should ONLY see surveys where surveyor_id = 2
4. Try to access another survey's URL directly
   Expected: 403 Forbidden or redirect
```

### ✅ Test 2: Admin Access
```
1. Login as surveyor@flettons.com
2. Try to access /admin/surveys
3. Expected: Redirect to surveyor dashboard or 403
```

### ✅ Test 3: Client Restrictions
```
1. Login as client@flettons.com
2. Try to access /admin/surveys
3. Expected: Redirect or 403
4. Try to access /surveyor/surveys
5. Expected: Redirect or 403
```

---

## Reset Password (If Needed)

If you need to reset any password:

```bash
php artisan tinker

# Reset admin password
$user = App\Models\User::where('email', 'admin@flettons.com')->first();
$user->password = Hash::make('newpassword');
$user->save();

# Reset surveyor password
$user = App\Models\User::where('email', 'surveyor@flettons.com')->first();
$user->password = Hash::make('newpassword');
$user->save();
```

---

## Reseed Database

If you need to start fresh:

```bash
# Reset everything and reseed
php artisan migrate:fresh --seed

# Or just reseed (without dropping tables)
php artisan db:seed --force
```

---

## Application URLs

### Main Routes
```
Login:          http://localhost:8000/login
Logout:         POST http://localhost:8000/logout
Home:           http://localhost:8000/
```

### Admin Routes
```
Dashboard:      http://localhost:8000/admin/dashboard
Surveys:        http://localhost:8000/admin/surveys
Survey Detail:  http://localhost:8000/admin/surveys/{id}
Edit Survey:    http://localhost:8000/admin/surveys/{id}/edit
Users:          http://localhost:8000/admin/users
Create User:    http://localhost:8000/admin/users/create
```

### Surveyor Routes
```
Dashboard:      http://localhost:8000/surveyor/dashboard
My Surveys:     http://localhost:8000/surveyor/surveys
Survey Detail:  http://localhost:8000/surveyor/surveys/{id}
```

### Client Routes
```
Dashboard:      http://localhost:8000/client/dashboard
My Surveys:     http://localhost:8000/client/surveys
Survey Detail:  http://localhost:8000/client/surveys/{id}
```

### NewDesign Pages (Static)
```
Categories:     http://localhost:8000/newdesign/pages/new-survey.html
Roofs Form:     http://localhost:8000/newdesign/pages/roof.html
```

---

## Sample Survey IDs for Testing

Based on seeded data:

```
Survey #1 - John Smith
Status: assigned
Surveyor: John Surveyor (ID: 2)
URL: /surveyor/surveys/1

Survey #2 - Sarah Johnson
Status: in_progress
Surveyor: John Surveyor (ID: 2)
URL: /surveyor/surveys/2

Survey #3 - Michael Brown
Status: pending (initially)
Surveyor: Not assigned (initially)
Use this for assignment testing

Survey #4 - Emma Williams
Status: completed
Surveyor: John Surveyor (ID: 2)
URL: /surveyor/surveys/4

Survey #5 - David Taylor
Status: pending
Surveyor: Not assigned
Use this for assignment testing
```

---

## Quick Commands

```bash
# Start server
php artisan serve

# Clear cache
php artisan cache:clear

# Check routes
php artisan route:list

# View migrations
php artisan migrate:status

# Reseed
php artisan db:seed --class=SurveySeeder

# Fresh start
php artisan migrate:fresh --seed

# Open tinker
php artisan tinker

# View logs
tail -f storage/logs/laravel.log
```

---

**Remember**: All test passwords are `password`

**Server**: Run `php artisan serve` first

**First Time**: Run `php artisan migrate:fresh --seed` to set up everything

---

Last Updated: October 21, 2025


