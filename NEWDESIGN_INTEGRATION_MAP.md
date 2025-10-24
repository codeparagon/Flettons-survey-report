# NewDesign Pages Integration Map

## Current State vs Future State

```
═══════════════════════════════════════════════════════════════════
                    CURRENT STATE (Static HTML)
═══════════════════════════════════════════════════════════════════

Surveyor Dashboard (Laravel)
    ↓
/surveyor/surveys/{id}
    ↓
[Manual Navigation - Open New Tab]
    ↓
/newdesign/pages/new-survey.html (Static)
    ↓
Click Category → /newdesign/pages/roof.html (Static)
    ↓
Fill Form → Data NOT saved to Laravel database
    ↓
Manual status update in surveyor dashboard


═══════════════════════════════════════════════════════════════════
                    FUTURE STATE (Integrated)
═══════════════════════════════════════════════════════════════════

Surveyor Dashboard (Laravel)
    ↓
/surveyor/surveys/{id}
    ↓
[Start Survey Button]
    ↓
/surveyor/survey/{id}/categories (Blade Template)
    ↓
Click Category → /surveyor/survey/{id}/category/roofs (Blade Template)
    ↓
Fill Form → Data SAVED to Laravel database
    ↓
Auto-update status when complete
```

---

## NewDesign Files Location

### Current Structure
```
public/
└── newdesign/
    ├── assets/
    │   ├── libs/
    │   │   └── css/
    │   │       └── style.css          ← Main stylesheet
    │   └── vendor/
    │       ├── bootstrap/
    │       ├── jquery/
    │       └── fonts/
    └── pages/
        ├── new-survey.html            ← Category selection
        ├── roof.html                  ← Roofs form ✅
        ├── walls-form.html            ← To be created
        ├── floors-form.html           ← To be created
        ├── doors-form.html            ← To be created
        ├── windows-form.html          ← To be created
        ├── interiors-form.html        ← To be created
        └── utilities-form.html        ← To be created
```

---

## Integration Roadmap

### Phase 1: Current Access (NOW) ✅
```
Surveyor logs in → Views assigned survey → Opens newdesign pages in new tab

Laravel Routes:                    NewDesign Pages (Static):
/surveyor/surveys/{id}      →      /newdesign/pages/new-survey.html
                                   /newdesign/pages/roof.html

Status: SEPARATE SYSTEMS
Data: NOT CONNECTED
```

### Phase 2: Basic Integration (Next Step)
```
Convert newdesign HTML → Blade templates
Add authentication
Pass survey ID to templates

Old: /newdesign/pages/new-survey.html
New: /surveyor/survey/{survey}/categories

Status: INTEGRATED NAVIGATION
Data: STILL NOT SAVED
```

### Phase 3: Data Integration
```
Create survey category tables
Save form data to database
Link forms to survey records

Status: FULLY INTEGRATED
Data: SAVED TO DATABASE
```

---

## File Conversion Plan

### Step 1: Convert new-survey.html to Blade

**From:**
```
public/newdesign/pages/new-survey.html
```

**To:**
```
resources/views/surveyor/survey/categories.blade.php
```

**Route:**
```php
Route::get('/surveyor/survey/{survey}/categories', 
    [SurveyorSurveyController::class, 'showCategories'])
    ->name('surveyor.survey.categories');
```

**Controller:**
```php
public function showCategories(Survey $survey)
{
    // Check authorization
    if ($survey->surveyor_id !== auth()->id()) {
        abort(403);
    }
    
    return view('surveyor.survey.categories', compact('survey'));
}
```

---

### Step 2: Convert roof.html to Blade

**From:**
```
public/newdesign/pages/roof.html
```

**To:**
```
resources/views/surveyor/survey/categories/roofs.blade.php
```

**Route:**
```php
Route::get('/surveyor/survey/{survey}/category/roofs', 
    [SurveyorSurveyController::class, 'showRoofs'])
    ->name('surveyor.survey.category.roofs');

Route::post('/surveyor/survey/{survey}/category/roofs', 
    [SurveyorSurveyController::class, 'saveRoofs'])
    ->name('surveyor.survey.category.roofs.save');
```

**Controller:**
```php
public function showRoofs(Survey $survey)
{
    if ($survey->surveyor_id !== auth()->id()) {
        abort(403);
    }
    
    $roofData = $survey->roofData; // Relationship
    
    return view('surveyor.survey.categories.roofs', 
        compact('survey', 'roofData'));
}

public function saveRoofs(Request $request, Survey $survey)
{
    if ($survey->surveyor_id !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'roof_type' => 'required',
        'roof_covering' => 'required',
        'condition' => 'required',
        // ... more fields
    ]);
    
    $survey->roofData()->updateOrCreate(
        ['survey_id' => $survey->id],
        $validated
    );
    
    return redirect()
        ->route('surveyor.survey.categories', $survey)
        ->with('success', 'Roofs data saved');
}
```

---

## Database Schema for Category Data

### survey_roofs Table
```php
Schema::create('survey_roofs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('survey_id')->constrained()->onDelete('cascade');
    
    // Basic info
    $table->string('roof_type')->nullable(); // pitched, flat, mixed
    $table->string('roof_covering')->nullable(); // tiles, slates, metal
    $table->string('condition')->nullable(); // good, fair, poor
    
    // Details
    $table->text('defects_noted')->nullable();
    $table->string('gutters_condition')->nullable();
    $table->text('recommendations')->nullable();
    
    // Photos
    $table->json('photos')->nullable(); // Array of photo paths
    
    // Completion
    $table->boolean('is_completed')->default(false);
    $table->timestamp('completed_at')->nullable();
    
    $table->timestamps();
});
```

### Model Relationship
```php
// In Survey.php
public function roofData()
{
    return $this->hasOne(SurveyRoof::class);
}

public function wallData()
{
    return $this->hasOne(SurveyWall::class);
}

// ... etc for each category
```

---

## NewDesign Page Structure

### new-survey.html Structure
```html
<div class="sections-row">
    <div class="col-xl-3">
        <a href="/pages/roof.html" class="section-card">
            <img src="../assets/vendor/new-survy-icon/1.png" alt="Roofs">
            <span>Roofs</span>
        </a>
    </div>
    <!-- More categories... -->
</div>
```

### Converted to Blade
```blade
<div class="sections-row">
    <div class="col-xl-3">
        <a href="{{ route('surveyor.survey.category.roofs', $survey) }}" 
           class="section-card {{ $survey->roofData?->is_completed ? 'completed' : '' }}">
            <img src="{{ asset('newdesign/assets/vendor/new-survy-icon/1.png') }}" 
                 alt="Roofs">
            <span>Roofs</span>
            @if($survey->roofData?->is_completed)
                <i class="fas fa-check-circle text-success"></i>
            @endif
        </a>
    </div>
    <!-- More categories... -->
</div>
```

---

## Navigation Flow (Integrated)

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Surveyor Dashboard                                        │
│    /surveyor/dashboard                                       │
│                                                              │
│    My Surveys:                                              │
│    [ Survey #1 - John Smith - View Details ]                │
└────────────────────────┬─────────────────────────────────────┘
                         │ Click "View Details"
                         ↓
┌──────────────────────────────────────────────────────────────┐
│ 2. Survey Details                                            │
│    /surveyor/surveys/1                                       │
│                                                              │
│    Client: John Smith                                       │
│    Property: 123 High St                                    │
│    Status: Assigned                                         │
│                                                              │
│    [ Start Survey ] ← New Button                           │
└────────────────────────┬─────────────────────────────────────┘
                         │ Click "Start Survey"
                         ↓
┌──────────────────────────────────────────────────────────────┐
│ 3. Survey Categories                                         │
│    /surveyor/survey/1/categories                            │
│    (Converted from new-survey.html)                         │
│                                                              │
│    Survey: #1 - John Smith                                 │
│    Progress: 2/7 Completed                                  │
│                                                              │
│    [ Roofs ✓ ]    [ Walls ]      [ Floors ]               │
│    [ Doors ]      [ Windows ✓ ]  [ Interiors ]            │
│    [ Utilities ]                                            │
└────────────────────────┬─────────────────────────────────────┘
                         │ Click "Roofs"
                         ↓
┌──────────────────────────────────────────────────────────────┐
│ 4. Roofs Survey Form                                         │
│    /surveyor/survey/1/category/roofs                        │
│    (Converted from roof.html)                               │
│                                                              │
│    Survey: #1 - John Smith - Roofs                         │
│                                                              │
│    Roof Type: ⦿ Pitched ○ Flat                            │
│    Covering:  ⦿ Tiles ○ Slates                            │
│    Condition: ⦿ Good ○ Fair ○ Poor                        │
│    Defects: [Text area]                                     │
│    Photos: [Upload] 📷                                      │
│                                                              │
│    [ Save & Back to Categories ]                            │
└────────────────────────┬─────────────────────────────────────┘
                         │ Click "Save"
                         ↓
┌──────────────────────────────────────────────────────────────┐
│ 5. Back to Categories (Data Saved)                          │
│    /surveyor/survey/1/categories                            │
│                                                              │
│    ✓ Success: Roofs data saved                             │
│                                                              │
│    [ Roofs ✓ ]    [ Walls ]      [ Floors ]               │
│    Progress: 3/7 Completed                                  │
└──────────────────────────────────────────────────────────────┘
```

---

## CSS and Assets Integration

### Keep NewDesign Theme
```blade
{{-- In your Blade layout --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/circular-std/style.css') }}">
    <link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('newdesign/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('newdesign/assets/libs/js/main-js.js') }}"></script>
@endsection
```

**No custom CSS - use only newdesign styles** ✅

---

## Photo Upload Implementation

### Form
```blade
<form action="{{ route('surveyor.survey.category.roofs.save', $survey) }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    
    <!-- ... form fields ... -->
    
    <div class="form-group">
        <label>Photos</label>
        <input type="file" 
               name="photos[]" 
               multiple 
               accept="image/*"
               class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">Save Section</button>
</form>
```

### Controller
```php
public function saveRoofs(Request $request, Survey $survey)
{
    $validated = $request->validate([
        'photos.*' => 'nullable|image|max:5120', // 5MB max
        // ... other fields
    ]);
    
    $photoPaths = [];
    
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store("surveys/{$survey->id}/roofs", 'public');
            $photoPaths[] = $path;
        }
    }
    
    $validated['photos'] = json_encode($photoPaths);
    
    $survey->roofData()->updateOrCreate(
        ['survey_id' => $survey->id],
        $validated
    );
    
    return redirect()->back()->with('success', 'Saved!');
}
```

---

## Progress Tracking

### Track Category Completion
```blade
@php
$completedCount = 0;
$totalCategories = 7;

if ($survey->roofData?->is_completed) $completedCount++;
if ($survey->wallData?->is_completed) $completedCount++;
if ($survey->floorData?->is_completed) $completedCount++;
// ... etc

$progressPercent = ($completedCount / $totalCategories) * 100;
@endphp

<div class="progress mb-4">
    <div class="progress-bar" 
         style="width: {{ $progressPercent }}%">
        {{ $completedCount }}/{{ $totalCategories }} Categories Completed
    </div>
</div>

@if($completedCount === $totalCategories)
    <button class="btn btn-success btn-lg" 
            onclick="markSurveyComplete()">
        <i class="fas fa-check"></i> Mark Survey as Complete
    </button>
@endif
```

---

## Testing Checklist (After Integration)

### ✅ Navigation Flow
- [ ] Click survey details → Start Survey button visible
- [ ] Click Start Survey → Redirects to categories page
- [ ] Click category → Redirects to category form
- [ ] Fill form → Click Save → Returns to categories
- [ ] Completed category shows checkmark

### ✅ Data Persistence
- [ ] Fill roof form → Save → Data appears in database
- [ ] Return to form → Previously saved data loads
- [ ] Upload photos → Photos saved to storage
- [ ] Photos display in admin view

### ✅ Progress Tracking
- [ ] Complete category → Progress updates
- [ ] All categories complete → "Complete Survey" button shows
- [ ] Mark complete → Survey status changes

### ✅ Security
- [ ] Surveyor can only access own surveys
- [ ] Direct URL access blocked for other surveyors
- [ ] Admin can view all category data

---

## URL Structure Summary

### Current (Static)
```
/newdesign/pages/new-survey.html
/newdesign/pages/roof.html
```

### Future (Integrated)
```
/surveyor/survey/{survey}/categories
/surveyor/survey/{survey}/category/roofs
/surveyor/survey/{survey}/category/walls
/surveyor/survey/{survey}/category/floors
/surveyor/survey/{survey}/category/doors
/surveyor/survey/{survey}/category/windows
/surveyor/survey/{survey}/category/interiors
/surveyor/survey/{survey}/category/utilities
```

---

## Next Development Steps

1. **Create Survey Category Models**
   - SurveyRoof.php
   - SurveyWall.php
   - etc.

2. **Create Migrations for Category Tables**
   - create_survey_roofs_table
   - create_survey_walls_table
   - etc.

3. **Convert HTML to Blade**
   - categories.blade.php (from new-survey.html)
   - categories/roofs.blade.php (from roof.html)
   - etc.

4. **Create Controller Methods**
   - showCategories()
   - showRoofs()
   - saveRoofs()
   - etc.

5. **Add Routes**
   - Define all category routes
   - Add save routes

6. **Test Integration**
   - Test navigation
   - Test data saving
   - Test photo upload
   - Test progress tracking

---

**Current Status**: Phase 1 ✅ (Separate systems)  
**Next Phase**: Phase 2 (Basic integration)  
**Design Theme**: NewDesign styles only ✅  
**No Custom CSS**: Confirmed ✅


