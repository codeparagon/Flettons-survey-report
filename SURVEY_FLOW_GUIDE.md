# Survey Flow Guide - SurvAI System

## Overview
This document explains the complete survey workflow from survey creation on an external website to survey execution by surveyors using the newdesign interface.

---

## System Architecture

### External Survey Website
- **Purpose**: Client-facing website where customers request surveys
- **Process**: Clients fill out survey request forms with property details
- **Data Transfer**: Survey data is sent to this system via API or database sync
- **Result**: Creates records in the `surveys` table

### Internal Management System (This Application)
- **Purpose**: Admin and surveyor management platform
- **Users**:
  - **Super Admin**: Manages surveys and assigns them to surveyors
  - **Surveyor**: Conducts surveys assigned to them
  - **Client**: Views their survey status (read-only)

---

## Complete Survey Flow

### Step 1: Survey Creation (External Website)
**Location**: External customer-facing website

1. Customer visits the external survey booking website
2. Customer fills out the survey request form:
   - Personal details (name, email, phone)
   - Property address
   - Property details (bedrooms, market value, etc.)
   - Survey level required (Level 1, 2, 3, or 4)
   - Special concerns or requirements
3. Customer completes payment
4. Survey record is created in the `surveys` table with:
   ```
   status: 'pending'
   payment_status: 'paid'
   surveyor_id: null
   ```

---

### Step 2: Admin Assignment
**Location**: `Admin Panel > Surveys > Edit Survey`  
**Route**: `/admin/surveys/{survey}/edit`  
**User**: Super Admin

1. Admin logs into the system
2. Navigates to: **Admin Dashboard** → **Surveys**
3. Sees list of all surveys with status indicators:
   - 🟡 **Pending**: Not yet assigned
   - 🔵 **Assigned**: Assigned to surveyor but not started
   - 🟣 **In Progress**: Surveyor is working on it
   - 🟢 **Completed**: Survey finished
   - 🔴 **Cancelled**: Cancelled survey

4. Admin clicks on a pending survey to view details
5. Clicks **Edit** button
6. Selects a surveyor from the dropdown (only users with 'surveyor' role)
7. Sets:
   - **Surveyor**: John Doe (example)
   - **Status**: `assigned`
   - **Scheduled Date**: 2025-10-25
   - **Admin Notes**: Any special instructions
8. Clicks **Update Survey**

**Result**: Survey status changes to `assigned` and `surveyor_id` is set

---

### Step 3: Surveyor Views Assigned Surveys
**Location**: `Surveyor Dashboard > My Surveys`  
**Route**: `/surveyor/surveys`  
**User**: Surveyor

1. Surveyor logs into the system
2. Dashboard shows only surveys assigned to them:
   ```php
   Survey::where('surveyor_id', auth()->id())
   ```
3. Surveyor can see:
   - Client name and contact info
   - Property address
   - Survey level
   - Scheduled date
   - Current status
4. Surveyor clicks on a survey to view full details

**Security**: Surveyors can ONLY see surveys where `surveyor_id` matches their user ID

---

### Step 4: Surveyor Accesses New Survey Interface
**Location**: `Public NewDesign Pages > New Survey`  
**Route**: `/newdesign/pages/new-survey.html` (to be integrated)  
**User**: Surveyor

#### Current NewDesign Page Structure:

The `new-survey.html` page displays survey categories:

```
┌─────────────────────────────────────────┐
│         SurvAI - New Survey             │
├─────────────────────────────────────────┤
│                                         │
│  [🏠 Roofs]    [🧱 Walls]    [📐 Floors] │
│                                         │
│  [🚪 Doors]    [🪟 Windows]  [🛋️ Interiors] │
│                                         │
│  [⚡ Utilities]                         │
│                                         │
└─────────────────────────────────────────┘
```

**Survey Categories Available:**

1. **Roofs** → `/newdesign/pages/roof.html`
   - Roof type, condition, materials
   - Tiles/slates assessment
   - Gutters and drainage
   - Chimney inspection

2. **Walls** → `walls-form.html` (to be created)
   - Exterior wall condition
   - Cracks and defects
   - Damp issues
   - Pointing and brickwork

3. **Floors** → `floors-form.html` (to be created)
   - Floor type and condition
   - Level and stability
   - Joists inspection
   - Subfloor ventilation

4. **Doors** → `doors-form.html` (to be created)
   - Door condition and operation
   - Frames and thresholds
   - Locks and security
   - Weatherproofing

5. **Windows** → `windows-form.html` (to be created)
   - Window type and condition
   - Glazing assessment
   - Frames and seals
   - Operation and locks

6. **Interiors** → `interiors-form.html` (to be created)
   - Internal walls and ceilings
   - Plastering condition
   - Decorative state
   - Built-in fixtures

7. **Utilities** → `utilities-form.html` (to be created)
   - Electrical system
   - Plumbing and heating
   - Gas installation
   - Drainage system

---

### Step 5: Surveyor Conducts Survey
**Location**: Property site (physical location)  
**Tool**: Mobile device or tablet with access to newdesign pages

1. Surveyor arrives at the property on scheduled date
2. Opens the survey in the system (mobile-friendly)
3. Clicks on each category to document findings:

#### Example: Roofs Survey
```
Route: /newdesign/pages/roof.html

Surveyor fills out:
- Roof type: Pitched/Flat
- Main roof covering: Tiles/Slates/Metal
- Condition rating: Good/Fair/Poor
- Specific defects noted
- Photos uploaded
- Recommendations
```

4. Surveyor completes all relevant categories
5. Each section is saved automatically or via "Save" button
6. Surveyor can return to new-survey.html to select next category

---

### Step 6: Survey Completion
**Location**: Surveyor Dashboard  
**Route**: `/surveyor/surveys/{survey}`  
**User**: Surveyor

1. After completing all survey sections on-site
2. Surveyor returns to the survey detail page
3. Updates status to **Completed**:
   ```
   Status: completed
   ```
4. System notifies admin and client

---

### Step 7: Admin Review & Report Generation
**Location**: Admin Panel  
**Route**: `/admin/surveys/{survey}`  
**User**: Super Admin

1. Admin sees survey marked as `completed`
2. Reviews all survey data collected
3. Generates final report (future feature)
4. Sends report to client
5. Archives survey

---

## Database Flow

```sql
-- 1. Survey created from external website
INSERT INTO surveys (
    first_name, last_name, email_address,
    full_address, postcode, number_of_bedrooms,
    status, payment_status, surveyor_id
) VALUES (
    'John', 'Smith', 'john@example.com',
    '123 High St', 'SW1A 1AA', 3,
    'pending', 'paid', NULL
);

-- 2. Admin assigns surveyor
UPDATE surveys 
SET surveyor_id = 2, 
    status = 'assigned',
    scheduled_date = '2025-10-25'
WHERE id = 1;

-- 3. Surveyor starts work
UPDATE surveys 
SET status = 'in_progress'
WHERE id = 1;

-- 4. Surveyor completes survey
UPDATE surveys 
SET status = 'completed'
WHERE id = 1;
```

---

## Integration Points

### Current State:
✅ Survey model with all fields  
✅ Admin can assign surveys to surveyors  
✅ Surveyors can only see their surveys  
✅ NewDesign pages exist with category selection  
✅ Status tracking system  

### To Be Implemented:
🔲 Link newdesign pages to Laravel routes  
🔲 Pass survey ID to newdesign pages  
🔲 Save survey section data to database  
🔲 Create remaining category forms (walls, floors, etc.)  
🔲 Photo upload functionality  
🔲 Report generation  
🔲 Email notifications  

---

## Seeding Test Data

To populate the database with sample surveys for testing:

```bash
# Run migrations
php artisan migrate:fresh

# Seed roles, users, and surveys
php artisan db:seed
```

This will create:
- 3 roles (Super Admin, Surveyor, Client)
- Sample users for each role
- 5 sample surveys with different statuses

**Test Surveys Created:**
1. John Smith - Assigned to surveyor - Status: Assigned
2. Sarah Johnson - In progress - Status: In Progress  
3. Michael Brown - Pending assignment - Status: Pending
4. Emma Williams - Already completed - Status: Completed
5. David Taylor - Not assigned yet - Status: Pending

---

## User Roles & Permissions

### Super Admin
- ✅ View all surveys
- ✅ Edit any survey
- ✅ Assign surveys to surveyors
- ✅ Change survey status
- ✅ Manage users
- ✅ View reports

### Surveyor
- ✅ View only their assigned surveys
- ✅ Update survey status (assigned → in_progress → completed)
- ✅ Fill out survey category forms
- ✅ Upload photos
- ❌ Cannot assign surveys
- ❌ Cannot see other surveyors' work

### Client
- ✅ View their own surveys
- ✅ See survey status
- ✅ Download reports when ready
- ❌ Cannot edit anything
- ❌ Read-only access

---

## Routes Summary

### Admin Routes
```
GET  /admin/surveys              - List all surveys
GET  /admin/surveys/{id}         - View survey details
GET  /admin/surveys/{id}/edit    - Edit survey form
PUT  /admin/surveys/{id}         - Update survey
```

### Surveyor Routes
```
GET  /surveyor/surveys           - List my assigned surveys only
GET  /surveyor/surveys/{id}      - View survey details
POST /surveyor/surveys/{id}/status - Update status
```

### NewDesign Survey Pages (Static - To be integrated)
```
GET  /newdesign/pages/new-survey.html  - Category selection
GET  /newdesign/pages/roof.html        - Roofs survey form
GET  /newdesign/pages/walls-form.html  - Walls survey form (TBC)
GET  /newdesign/pages/floors-form.html - Floors survey form (TBC)
... etc
```

---

## Security Features

1. **Role-based Access Control**: Middleware checks user roles
2. **Surveyor Isolation**: Surveyors can only access their own surveys
3. **Authorization Checks**: 
   ```php
   if ($survey->surveyor_id !== auth()->id()) {
       abort(403, 'Unauthorized');
   }
   ```
4. **Status Validation**: Only certain status transitions allowed
5. **Payment Verification**: Surveys require payment before surveyor access

---

## Next Steps for Integration

1. **Create Laravel Controllers for NewDesign Pages**:
   ```php
   Route::get('/surveyor/survey/{survey}/category/{category}', 
       [SurveyorSurveyController::class, 'showCategory']);
   ```

2. **Convert Static HTML to Blade Templates**:
   - Move `newdesign/pages/*.html` to `resources/views/surveyor/survey/`
   - Add Laravel authentication and authorization
   - Integrate with survey data

3. **Create Survey Category Data Models**:
   - Create migrations for category-specific tables
   - Link to main survey record

4. **Add Photo Upload**:
   - File upload to `storage/app/surveys/{survey_id}/`
   - Thumbnail generation

5. **Implement Report Generation**:
   - PDF generation using Laravel packages
   - Email delivery to clients

---

## Testing the Flow

### Test as Admin:
1. Login: `admin@example.com` / password from UserSeeder
2. Go to Admin → Surveys
3. Edit a pending survey
4. Assign to a surveyor
5. Set scheduled date

### Test as Surveyor:
1. Login: `surveyor@example.com` / password from UserSeeder
2. Go to Surveyor Dashboard
3. See only assigned surveys
4. Click on a survey
5. Update status to "In Progress"
6. Navigate to `/newdesign/pages/new-survey.html` (separate window for now)
7. Select a category (e.g., Roofs)
8. Fill out the form
9. Return and mark as "Completed"

---

## Design Theme Usage

As per your preferences, all integrated pages will use:
- **Theme**: `public/newdesign/`
- **Styles**: `newdesign/assets/libs/css/style.css` only
- **No custom CSS modifications**

The newdesign pages provide a modern, mobile-responsive interface perfect for on-site survey work.

---

## Questions & Support

For any questions about the survey flow or integration:
1. Check this guide first
2. Review the database migrations and models
3. Test with seeded data
4. Refer to the newdesign HTML templates for UI reference

---

**Last Updated**: October 21, 2025  
**Version**: 1.0  
**System**: SurvAI - Survey Management Platform


