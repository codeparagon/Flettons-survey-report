# Quick Start Guide - Testing Survey Flow

## Prerequisites

1. **Database Setup**
```bash
# Run migrations (if not already done)
php artisan migrate

# Seed the database with test data
php artisan db:seed
```

This will create:
- 3 Roles (Super Admin, Surveyor, Client)
- Sample users for each role
- 5 sample surveys with different statuses

---

## Test Accounts

### Super Admin Account
```
Email: admin@example.com
Password: [Check UserSeeder.php for password]
```

### Surveyor Account  
```
Email: surveyor@example.com
Password: [Check UserSeeder.php for password]
```

### Client Account
```
Email: client@example.com
Password: [Check UserSeeder.php for password]
```

---

## Testing the Complete Flow

### Part 1: Admin Assigns Survey

1. **Login as Admin**
   - Navigate to: `http://your-domain.com/login`
   - Use admin credentials
   - You'll be redirected to: `/admin/dashboard`

2. **View All Surveys**
   - Click "Surveys" in sidebar (or go to `/admin/surveys`)
   - You should see 5 surveys with different statuses:
     ```
     ID  Client              Status         Surveyor
     #1  John Smith         Assigned       Assigned
     #2  Sarah Johnson      In Progress    Assigned
     #3  Michael Brown      Pending        Not Assigned
     #4  Emma Williams      Completed      Assigned
     #5  David Taylor       Pending        Not Assigned
     ```

3. **Edit/Assign a Survey**
   - Click on survey #3 (Michael Brown - Pending)
   - Click "Edit" button
   - You'll see the edit form:
     ```
     Surveyor: [Select Surveyor ▼]
     Status: [pending ▼]
     Payment Status: [paid ▼]
     Scheduled Date: [Date Picker]
     Admin Notes: [Text Area]
     ```
   - Select a surveyor from dropdown
   - Change status to "assigned"
   - Set a scheduled date
   - Add admin notes: "Listed building - Grade II"
   - Click "Update Survey"
   - ✅ Survey is now assigned!

### Part 2: Surveyor Views Assignment

1. **Logout from Admin**
   - Click profile → Logout

2. **Login as Surveyor**
   - Use surveyor credentials
   - You'll be redirected to: `/surveyor/dashboard`

3. **View My Surveys**
   - Click "Surveys" in sidebar (or go to `/surveyor/surveys`)
   - You should ONLY see surveys assigned to you
   - Example:
     ```
     My Assigned Surveys (3)
     
     #1  John Smith
         123 High Street, London SW1A 1AA
         Level 2 Survey | Scheduled: Oct 25, 2025
         Status: Assigned
         [View Details]
     
     #2  Sarah Johnson
         45 Park Avenue, Manchester M1 2AB
         Level 3 Survey | Scheduled: Oct 22, 2025
         Status: In Progress
         [View Details]
     ```
   - You won't see surveys assigned to other surveyors
   - You won't see unassigned surveys

4. **View Survey Details**
   - Click "View Details" on survey #1
   - You'll see:
     - Client information
     - Property details
     - Survey level
     - Scheduled date
     - Current status
   - Click "Update Status" button
   - Change status to "In Progress"
   - ✅ Status updated!

### Part 3: Access New Survey Interface

**Option A: Static HTML (Current State)**

1. While logged in as surveyor, open new tab:
   ```
   http://your-domain.com/newdesign/pages/new-survey.html
   ```

2. You'll see the category selection page:
   ```
   ┌─────────────────────────────────┐
   │     Sections                    │
   ├─────────────────────────────────┤
   │  [Roofs]    [Walls]   [Floors] │
   │  [Doors]    [Windows] [Interiors] │
   │  [Utilities]                    │
   └─────────────────────────────────┘
   ```

3. Click "Roofs" to go to:
   ```
   http://your-domain.com/newdesign/pages/roof.html
   ```

4. Fill out the roof survey form

**Option B: Integrated (Future Implementation)**

Once integrated, the flow will be:
```
/surveyor/surveys/{id} 
  → [Start Survey Button] 
  → /surveyor/survey/{id}/categories
  → Click category 
  → /surveyor/survey/{id}/category/roofs
```

### Part 4: Complete Survey

1. After filling out all category forms
2. Return to `/surveyor/surveys/{id}`
3. Update status to "Completed"
4. ✅ Survey marked as complete!

### Part 5: Admin Review

1. Logout from surveyor account
2. Login as admin
3. Go to `/admin/surveys`
4. Click on the completed survey
5. Review all collected data
6. Generate report (future feature)

---

## Quick URL Reference

### Admin URLs
```
/admin/dashboard              - Admin dashboard
/admin/surveys                - List all surveys
/admin/surveys/{id}           - View survey details
/admin/surveys/{id}/edit      - Edit/assign survey
/admin/users                  - Manage users
/admin/users/create           - Create new user
```

### Surveyor URLs
```
/surveyor/dashboard           - Surveyor dashboard
/surveyor/surveys             - My assigned surveys only
/surveyor/surveys/{id}        - View survey details
/surveyor/surveys/{id}/status - Update survey status (POST)
```

### NewDesign Pages (Static - Current)
```
/newdesign/pages/new-survey.html  - Category selection
/newdesign/pages/roof.html        - Roofs form
/newdesign/pages/walls-form.html  - Walls form (TBC)
/newdesign/pages/floors-form.html - Floors form (TBC)
```

### Client URLs
```
/client/dashboard             - Client dashboard
/client/surveys               - My surveys
/client/surveys/{id}          - View survey details
```

---

## Database Quick Check

### View All Surveys
```sql
SELECT 
    id,
    CONCAT(first_name, ' ', last_name) as client,
    full_address,
    status,
    surveyor_id,
    scheduled_date
FROM surveys
ORDER BY created_at DESC;
```

### View Survey with Surveyor
```sql
SELECT 
    s.id,
    CONCAT(s.first_name, ' ', s.last_name) as client,
    s.full_address,
    s.status,
    u.name as surveyor_name,
    s.scheduled_date
FROM surveys s
LEFT JOIN users u ON s.surveyor_id = u.id
ORDER BY s.created_at DESC;
```

### View Surveys by Status
```sql
SELECT status, COUNT(*) as count
FROM surveys
GROUP BY status;
```

### Check Surveyor's Assigned Surveys
```sql
-- Replace {surveyor_id} with actual ID (e.g., 2)
SELECT 
    id,
    CONCAT(first_name, ' ', last_name) as client,
    full_address,
    status,
    scheduled_date
FROM surveys
WHERE surveyor_id = {surveyor_id}
ORDER BY scheduled_date;
```

---

## Troubleshooting

### Issue: Can't see any surveys as surveyor

**Problem**: Surveyor sees empty list

**Solution**: 
1. Login as admin
2. Go to `/admin/surveys`
3. Edit a survey
4. Assign it to your surveyor account
5. Logout and login as surveyor again

### Issue: Forbidden 403 error when accessing survey

**Problem**: "Unauthorized" error when clicking survey details

**Cause**: Trying to access a survey not assigned to you

**Solution**: Make sure you're accessing a survey where `surveyor_id` matches your user ID

### Issue: Static newdesign pages not styled

**Problem**: newdesign pages show unstyled content

**Solution**: Check that paths are correct:
- CSS: `/newdesign/assets/libs/css/style.css`
- JS: `/newdesign/assets/vendor/jquery/jquery-3.3.1.min.js`
- Make sure you're accessing via proper domain, not file://

### Issue: Database empty after seeding

**Problem**: No surveys showing

**Solution**:
```bash
# Check if seeder ran
php artisan db:seed --class=SurveySeeder

# If still empty, check migrations
php artisan migrate:status

# Refresh everything
php artisan migrate:fresh --seed
```

---

## Sample Survey Data Reference

### Survey #1 - John Smith
- **Property**: 123 High Street, London SW1A 1AA
- **Type**: House, 3 bedrooms
- **Value**: £450,000
- **Level**: Level 2
- **Status**: Assigned
- **Concerns**: Minor cracks in exterior walls

### Survey #2 - Sarah Johnson
- **Property**: 45 Park Avenue, Manchester M1 2AB
- **Type**: Flat, 2 bedrooms
- **Value**: £280,000
- **Level**: Level 3
- **Status**: In Progress
- **Concerns**: Roof condition and dampness

### Survey #3 - Michael Brown
- **Property**: 78 Oak Drive, Birmingham B15 3TN
- **Type**: House, 4 bedrooms, Listed Building
- **Value**: £595,000
- **Level**: Level 3
- **Status**: Pending (initially)
- **Concerns**: Listed building - Grade II

### Survey #4 - Emma Williams
- **Property**: 12 Riverside Court, Leeds LS1 4BZ
- **Type**: Flat, 1 bedroom
- **Value**: £185,000
- **Level**: Level 1
- **Status**: Completed
- **Concerns**: None - first time buyer

### Survey #5 - David Taylor
- **Property**: 89 Victoria Road, Bristol BS1 6HY
- **Type**: House, 5 bedrooms, Pre-1700
- **Value**: £725,000
- **Level**: Level 4
- **Status**: Pending
- **Concerns**: Built pre-1700, extensive modifications

---

## Testing Checklist

### ✅ Admin Functions
- [ ] Login as admin
- [ ] View all surveys
- [ ] Edit a survey
- [ ] Assign survey to surveyor
- [ ] Change survey status
- [ ] Set scheduled date
- [ ] Add admin notes
- [ ] Create new user
- [ ] Manage users

### ✅ Surveyor Functions
- [ ] Login as surveyor
- [ ] View only my assigned surveys
- [ ] View survey details
- [ ] Update survey status
- [ ] Access newdesign pages
- [ ] Fill out category forms
- [ ] Mark survey as completed
- [ ] Cannot see other surveyors' surveys (security check)

### ✅ Security Tests
- [ ] Surveyor cannot access `/admin/*` routes
- [ ] Surveyor cannot view other surveyors' surveys
- [ ] Surveyor gets 403 when accessing unassigned survey directly
- [ ] Client can only see their own surveys
- [ ] Unauthenticated users redirected to login

---

## Next Steps for Development

1. **Integrate NewDesign Pages**:
   - Convert static HTML to Blade templates
   - Add authentication
   - Pass survey data to templates
   - Save form data to database

2. **Create Category Tables**:
   - `survey_roofs` table
   - `survey_walls` table
   - etc. for each category

3. **Add Photo Upload**:
   - File upload functionality
   - Image storage
   - Thumbnail generation

4. **Report Generation**:
   - PDF generation
   - Email delivery
   - Client download portal

---

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JS errors
3. Verify database connections
4. Ensure all migrations are run
5. Clear cache: `php artisan cache:clear`

---

**Last Updated**: October 21, 2025  
**Version**: 1.0  
**Status**: ✅ Seeded and Ready for Testing


