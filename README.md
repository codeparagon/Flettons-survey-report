# Flettons Dashboard - Survey Management System

Professional Laravel 10 dashboard for managing property surveys with role-based access control.

## 🚀 Quick Start

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit: **http://localhost:8000**

## 🔐 Login Credentials

| Role | Email | Password | Access |
|------|-------|----------|--------|
| **Super Admin** | admin@flettons.com | password | Manage surveys & users, assign surveyors |
| **Surveyor** | surveyor@flettons.com | password | View assigned surveys, update status |
| **Client** | client@flettons.com | password | View their surveys & reports |

## ✨ Features

### ✅ Authentication System
- Role-based access control (3 roles)
- Secure login with proper redirects
- Session management

### ✅ Survey Management
- **Surveys come from external platform** (not created here)
- Admin can view all survey applications
- Admin assigns surveyors to surveys
- Admin manages survey status and payment
- Surveyors view assigned surveys
- Surveyors update survey status
- Clients view their survey applications

### ✅ User Management
- Create, edit, delete users
- Assign roles
- Manage user status

## 📊 Survey Workflow

1. **Client** submits survey application via external platform
2. **Survey** appears in admin dashboard automatically
3. **Admin** assigns surveyor to the survey
4. **Admin** schedules survey date
5. **Surveyor** views assigned survey
6. **Surveyor** updates status (assigned → in_progress → completed)
7. **Client** views survey status in dashboard

## 📁 Database

### Core Tables
- `users` - User accounts with roles
- `roles` - User roles (super_admin, surveyor, client)
- `surveys` - Survey applications (from external platform)

### Survey Fields
The surveys table contains all fields from the external form including:
- Client information
- Property details
- Solicitor information
- Estate agent details
- Survey level and pricing
- Management fields (surveyor_id, status, scheduled_date, etc.)

## 🎯 Access Control

### Super Admin Can:
- ✅ View all survey applications
- ✅ Assign surveyors to surveys
- ✅ Update survey status
- ✅ Manage payment status
- ✅ Schedule survey dates
- ✅ Manage users and roles

### Surveyor Can:
- ✅ View surveys assigned to them
- ✅ Update survey status (assigned/in_progress/completed)
- ✅ View client information
- ✅ See scheduled dates

### Client Can:
- ✅ View their survey applications
- ✅ Track survey status
- ✅ See assigned surveyor
- ✅ View scheduled dates

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── SurveyController.php
│   │   │   └── UserController.php
│   │   ├── Surveyor/
│   │   │   ├── DashboardController.php
│   │   │   └── SurveyController.php
│   │   └── Client/
│   │       ├── DashboardController.php
│   │       └── SurveyController.php
│   └── Middleware/           # Role-based authorization
├── Models/
│   ├── User.php
│   ├── Role.php
│   └── Survey.php
├── Services/                 # Business logic layer
├── Repositories/             # Data access layer
└── Traits/                   # Reusable code

resources/views/
├── layouts/                  # Master layouts
├── admin/                    # Admin views
│   ├── surveys/              # Survey management
│   └── users/                # User management
├── surveyor/                 # Surveyor views
│   └── surveys/
└── client/                   # Client views
    └── surveys/
```

## 🛠️ Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# View all routes
php artisan route:list

# Reset database
php artisan migrate:fresh --seed

# Create new controller
php artisan make:controller ControllerName

# Create new model
php artisan make:model ModelName

# Link storage so uploaded images are publicly accessible (required on production)
php artisan storage:link
```

## 📷 Production: Survey section images not showing

Survey section images are stored under `storage/app/public/` and served at `https://your-domain.com/storage/...`. If images work locally but not on production, run these on the **server** (e.g. over SSH):

1. **Create the storage link** (when `symlink()` is allowed):
   ```bash
   cd /path/to/your/laravel/app
   php artisan storage:link
   ```
   This creates `public/storage` → `storage/app/public`. Without it, `/storage/...` URLs return 404.

2. **If `php artisan storage:link` fails with "Call to undefined function symlink()"**: Many shared hosts disable `symlink()`. This app includes a **fallback route** that serves files from `storage/app/public` at `/storage/*` without using a symlink. Deploy the latest code (which adds this route) and ensure requests to `/storage/...` are handled by Laravel (your server should send all non-static requests to `index.php`). No extra steps needed; images will be served by the route.

3. **Check permissions** so the web server can read files:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

4. **Confirm files exist**: Images only exist on the server if they were uploaded there. If you only deploy code, `storage/app/public/survey-photos/` may be empty. Either re-upload on production, or sync those folders from local to server (e.g. rsync), or use S3 for the public disk in production.

## 🔮 Ready for Future Development

The system is prepared for:
- AWS S3 integration (configured in .env)
- Transcription services
- GPT report generation
- Additional survey data tables
- Report PDF generation

## 📝 Notes

- Surveys are submitted via external platform and stored in `surveys` table
- Admin cannot create surveys (they come from external source)
- Admin can only manage and assign existing surveys
- Design uses original theme from `design/assets/libs/css/style.css`
- No custom CSS modifications

## 🔒 Security

- CSRF protection
- Input validation
- Role-based authorization
- Secure password hashing
- SQL injection prevention

---

**Version:** 1.0.0  
**Laravel:** 10.x  
**Status:** ✅ Clean & Production Ready
