# Survey Flow - Visual Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SURVEY FLOW - COMPLETE PROCESS                           │
└─────────────────────────────────────────────────────────────────────────────┘


STEP 1: EXTERNAL WEBSITE (Customer Booking)
┌─────────────────────────────────────────────────────────────────┐
│  📱 Customer Website (External)                                 │
│  ─────────────────────────────                                  │
│  Customer fills survey request form:                            │
│  • Name, Email, Phone                                           │
│  • Property Address                                             │
│  • Property Details (bedrooms, value, etc.)                     │
│  • Survey Level (1, 2, 3, or 4)                                │
│  • Payment                                                       │
│                                                                  │
│  ✓ Creates record in `surveys` table                           │
│    status: 'pending'                                            │
│    surveyor_id: NULL                                            │
│    payment_status: 'paid'                                       │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
                    [Survey Created]
                           │
                           ↓

STEP 2: ADMIN ASSIGNMENT
┌─────────────────────────────────────────────────────────────────┐
│  👨‍💼 Admin Panel                                                  │
│  ──────────────                                                  │
│  Route: /admin/surveys                                          │
│                                                                  │
│  Admin Dashboard:                                               │
│  ┌────────────────────────────────────────────────────┐        │
│  │ ID  Client          Property        Status         │        │
│  ├────────────────────────────────────────────────────┤        │
│  │ #1  John Smith     123 High St     🟡 Pending      │        │
│  │ #2  Sarah Johnson  45 Park Ave     🔵 Assigned     │        │
│  │ #3  Michael Brown  78 Oak Drive    🟣 In Progress  │        │
│  └────────────────────────────────────────────────────┘        │
│                                                                  │
│  Admin clicks survey → Edit → Assign:                          │
│  • Select Surveyor: [John Doe ▼]                               │
│  • Status: assigned                                             │
│  • Scheduled Date: 2025-10-25                                  │
│  • Admin Notes: "Check roof carefully"                         │
│  [Update Survey]                                                │
│                                                                  │
│  ✓ Updates survey record:                                      │
│    surveyor_id: 2                                               │
│    status: 'assigned'                                           │
│    scheduled_date: '2025-10-25'                                │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
                   [Survey Assigned]
                           │
                           ↓

STEP 3: SURVEYOR VIEWS ASSIGNMENT
┌─────────────────────────────────────────────────────────────────┐
│  👨‍🔧 Surveyor Dashboard                                          │
│  ────────────────────────                                        │
│  Route: /surveyor/surveys                                       │
│                                                                  │
│  My Assigned Surveys (ONLY mine):                              │
│  ┌────────────────────────────────────────────────────┐        │
│  │ #1  John Smith                                     │        │
│  │     123 High St, London SW1A 1AA                   │        │
│  │     Level 2 Survey                                 │        │
│  │     📅 Scheduled: Oct 25, 2025                     │        │
│  │     Status: 🔵 Assigned                            │        │
│  │     [View Details] [Start Survey]                 │        │
│  └────────────────────────────────────────────────────┘        │
│                                                                  │
│  ┌────────────────────────────────────────────────────┐        │
│  │ #2  Sarah Johnson                                  │        │
│  │     45 Park Ave, Manchester M1 2AB                 │        │
│  │     Level 3 Survey                                 │        │
│  │     📅 Scheduled: Oct 22, 2025                     │        │
│  │     Status: 🟣 In Progress                         │        │
│  │     [View Details] [Continue Survey]              │        │
│  └────────────────────────────────────────────────────┘        │
│                                                                  │
│  Security: WHERE surveyor_id = auth()->id()                    │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
                  [Surveyor Clicks Survey]
                           │
                           ↓

STEP 4: ON-SITE SURVEY - CATEGORY SELECTION
┌─────────────────────────────────────────────────────────────────┐
│  📱 New Survey Page (On-Site via Mobile/Tablet)                │
│  ───────────────────────────────────────────────                │
│  Route: /newdesign/pages/new-survey.html                       │
│                                                                  │
│  Survey for: John Smith - 123 High St                          │
│  Level: 2 | Date: Oct 25, 2025                                 │
│                                                                  │
│  ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓         │
│  ┃           Select Survey Category                ┃         │
│  ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛         │
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                    │
│  │   🏠     │  │   🧱     │  │   📐     │                    │
│  │  Roofs   │  │  Walls   │  │  Floors  │                    │
│  │  [View]  │  │  [View]  │  │  [View]  │                    │
│  └──────────┘  └──────────┘  └──────────┘                    │
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                    │
│  │   🚪     │  │   🪟     │  │   🛋️     │                    │
│  │  Doors   │  │ Windows  │ │ Interiors │                    │
│  │  [View]  │  │  [View]  │  │  [View]  │                    │
│  └──────────┘  └──────────┘  └──────────┘                    │
│                                                                  │
│  ┌──────────┐                                                   │
│  │   ⚡     │                                                   │
│  │ Utilities│                                                   │
│  │  [View]  │                                                   │
│  └──────────┘                                                   │
│                                                                  │
│  Progress: 3/7 Sections Completed                              │
│  [Save & Exit] [Complete Survey]                               │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
              [Surveyor Clicks "Roofs"]
                           │
                           ↓

STEP 5: CATEGORY DETAIL FORM
┌─────────────────────────────────────────────────────────────────┐
│  📝 Roofs Survey Form                                           │
│  ────────────────────                                            │
│  Route: /newdesign/pages/roof.html                             │
│                                                                  │
│  Property: 123 High St, London SW1A 1AA                        │
│  Category: Roofs                                                │
│                                                                  │
│  ┌─────────────────────────────────────────────────┐           │
│  │ 1. Roof Type:                                   │           │
│  │    ○ Pitched  ○ Flat  ○ Mixed                  │           │
│  │                                                  │           │
│  │ 2. Main Roof Covering:                          │           │
│  │    ○ Tiles  ○ Slates  ○ Metal  ○ Other        │           │
│  │                                                  │           │
│  │ 3. Overall Condition:                           │           │
│  │    ○ Good  ○ Fair  ○ Poor                      │           │
│  │                                                  │           │
│  │ 4. Specific Defects Noted:                     │           │
│  │    ┌─────────────────────────────────────┐     │           │
│  │    │ Minor wear on south-facing tiles    │     │           │
│  │    │ Small crack near chimney base       │     │           │
│  │    │                                      │     │           │
│  │    └─────────────────────────────────────┘     │           │
│  │                                                  │           │
│  │ 5. Gutters & Drainage:                         │           │
│  │    ○ Good  ○ Requires Attention  ○ Poor      │           │
│  │                                                  │           │
│  │ 6. Photos:                                      │           │
│  │    📷 [Upload Photos]                          │           │
│  │    [roof_1.jpg] [roof_2.jpg] [roof_3.jpg]     │           │
│  │                                                  │           │
│  │ 7. Recommendations:                             │           │
│  │    ┌─────────────────────────────────────┐     │           │
│  │    │ Monitor tiles, consider replacing   │     │           │
│  │    │ in next 2-3 years                   │     │           │
│  │    └─────────────────────────────────────┘     │           │
│  │                                                  │           │
│  │ [Save Section] [Back to Categories]            │           │
│  └─────────────────────────────────────────────────┘           │
│                                                                  │
│  ✓ Data saved to database                                      │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
              [Surveyor Returns to Categories]
                           │
                           ↓
              [Repeats for each category]
                           │
                           ↓

STEP 6: SURVEY COMPLETION
┌─────────────────────────────────────────────────────────────────┐
│  ✅ Complete Survey                                             │
│  ─────────────────────                                          │
│  Route: /surveyor/surveys/{survey}                             │
│                                                                  │
│  All sections completed:                                        │
│  ✓ Roofs      - Completed                                      │
│  ✓ Walls      - Completed                                      │
│  ✓ Floors     - Completed                                      │
│  ✓ Doors      - Completed                                      │
│  ✓ Windows    - Completed                                      │
│  ✓ Interiors  - Completed                                      │
│  ✓ Utilities  - Completed                                      │
│                                                                  │
│  Update Status:                                                 │
│  Current: 🟣 In Progress                                       │
│  Change to: [Completed ▼]                                      │
│                                                                  │
│  [Mark as Completed]                                            │
│                                                                  │
│  ✓ Updates survey:                                             │
│    status: 'completed'                                          │
│    completed_at: now()                                          │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
                  [Survey Completed]
                           │
                           ↓

STEP 7: ADMIN REVIEW
┌─────────────────────────────────────────────────────────────────┐
│  📊 Admin Review                                                │
│  ──────────────                                                  │
│  Route: /admin/surveys/{survey}                                │
│                                                                  │
│  Survey #1 - John Smith                                        │
│  Status: 🟢 Completed                                          │
│  Completed Date: Oct 25, 2025                                  │
│                                                                  │
│  Survey Data:                                                   │
│  • All 7 categories completed                                  │
│  • 24 photos uploaded                                          │
│  • Surveyor notes available                                    │
│                                                                  │
│  Actions:                                                       │
│  [Generate Report] [Email to Client] [Archive]                │
│                                                                  │
│  ✓ Report generated and sent to client                        │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ↓
                   [Process Complete]


═══════════════════════════════════════════════════════════════════
                         DATA FLOW
═══════════════════════════════════════════════════════════════════

External Website → surveys table → Admin assigns → Surveyor receives
     ↓                                                       ↓
  Payment                                            NewDesign Pages
  Complete                                                   ↓
                                                    Category Forms
                                                           ↓
                                                    Save Survey Data
                                                           ↓
                                                    Mark Completed
                                                           ↓
                                                    Admin Review
                                                           ↓
                                                    Report to Client


═══════════════════════════════════════════════════════════════════
                      DATABASE STATES
═══════════════════════════════════════════════════════════════════

Survey Record Status Progression:

┌─────────────────────────────────────────────────────────────────┐
│                                                                  │
│  🟡 PENDING                                                     │
│  └─→ Created from external website                             │
│      surveyor_id: NULL                                          │
│      status: 'pending'                                          │
│                    ↓                                             │
│                    │ Admin assigns surveyor                     │
│                    ↓                                             │
│  🔵 ASSIGNED                                                    │
│  └─→ Assigned to specific surveyor                             │
│      surveyor_id: [user_id]                                     │
│      status: 'assigned'                                         │
│      scheduled_date: [date]                                     │
│                    ↓                                             │
│                    │ Surveyor starts work                       │
│                    ↓                                             │
│  🟣 IN PROGRESS                                                 │
│  └─→ Surveyor is conducting survey                             │
│      status: 'in_progress'                                      │
│                    ↓                                             │
│                    │ All sections completed                     │
│                    ↓                                             │
│  🟢 COMPLETED                                                   │
│  └─→ Survey finished                                            │
│      status: 'completed'                                        │
│      completed_at: [timestamp]                                  │
│                    ↓                                             │
│                    │ Admin reviews & sends report               │
│                    ↓                                             │
│  📦 ARCHIVED                                                    │
│  └─→ Report delivered to client                                │
│                                                                  │
│                                                                  │
│  Alternative Path:                                              │
│  🔴 CANCELLED                                                   │
│  └─→ Survey cancelled by client or admin                       │
│      status: 'cancelled'                                        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════
                      TEST DATA SEEDED
═══════════════════════════════════════════════════════════════════

5 Sample Surveys Created:

1. John Smith - 123 High Street, London
   Status: 🔵 Assigned | Level 2 | £650
   Surveyor: Assigned | Scheduled: +3 days

2. Sarah Johnson - 45 Park Avenue, Manchester  
   Status: 🟣 In Progress | Level 3 | £850
   Surveyor: Assigned | Scheduled: +1 day

3. Michael Brown - 78 Oak Drive, Birmingham
   Status: 🟡 Pending | Level 3 | £1,250
   Surveyor: Not Assigned | Listed Building

4. Emma Williams - 12 Riverside Court, Leeds
   Status: 🟢 Completed | Level 1 | £450
   Surveyor: Completed -2 days ago

5. David Taylor - 89 Victoria Road, Bristol
   Status: 🟡 Pending | Level 4 | £1,450
   Surveyor: Not Assigned | Pre-1700 property


═══════════════════════════════════════════════════════════════════
                    SECURITY BOUNDARIES
═══════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────┐
│                                                                  │
│  SUPER ADMIN                                                    │
│  ────────────                                                    │
│  ✓ Access ALL surveys                                          │
│  ✓ Assign/reassign surveyors                                   │
│  ✓ Change any status                                           │
│  ✓ View all data                                               │
│  ✓ Generate reports                                            │
│  ✓ Manage users                                                │
│                                                                  │
│  ─────────────────────────────────────────────────             │
│                                                                  │
│  SURVEYOR                                                       │
│  ─────────                                                       │
│  ✓ Access ONLY assigned surveys                                │
│      WHERE surveyor_id = auth()->id()                           │
│  ✓ Update status (assigned → in_progress → completed)         │
│  ✓ Fill survey forms                                           │
│  ✓ Upload photos                                               │
│  ✗ Cannot see other surveyors' work                           │
│  ✗ Cannot assign surveys                                       │
│  ✗ Cannot access admin panel                                   │
│                                                                  │
│  ─────────────────────────────────────────────────             │
│                                                                  │
│  CLIENT                                                         │
│  ──────                                                          │
│  ✓ View OWN surveys only                                       │
│      WHERE email_address = auth()->user()->email                │
│  ✓ See survey status                                           │
│  ✓ Download completed reports                                  │
│  ✗ Cannot edit anything                                        │
│  ✗ Read-only access                                            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════
                  NEWDESIGN INTEGRATION PLAN
═══════════════════════════════════════════════════════════════════

Current State (Static HTML):
  /newdesign/pages/new-survey.html
  /newdesign/pages/roof.html
  
Future Integration (Laravel Blade):
  /surveyor/survey/{survey}/categories
  /surveyor/survey/{survey}/category/roofs
  /surveyor/survey/{survey}/category/walls
  /surveyor/survey/{survey}/category/floors
  ... etc

Design Theme: public/newdesign/assets/libs/css/style.css
No custom CSS - use newdesign styles only ✓


═══════════════════════════════════════════════════════════════════
```


