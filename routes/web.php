<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Surveyor\DashboardController as SurveyorDashboard;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Surveyor\AwsTranscriptionController;
use App\Http\Controllers\Surveyor\SurveyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Route - Redirect based on authentication
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSurveyor()) {
            // Default landing page for surveyors: My Surveys (jobs list)
            return redirect()->route('surveyor.surveys.index');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }
    }
    
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Super Admin Routes
Route::prefix('admin')->middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    
    // User Management
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    
    // Additional User Management Routes
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::get('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('admin.users.impersonate');
    Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('admin.users.activity');
    
    // Survey Management
    Route::get('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('admin.surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'show'])->name('admin.surveys.show');
    Route::get('/surveys/{survey}/sections', [\App\Http\Controllers\Admin\SurveyController::class, 'sections'])->name('admin.survey.sections');
    Route::get('/surveys/{survey}/edit', [\App\Http\Controllers\Admin\SurveyController::class, 'edit'])->name('admin.surveys.edit');
    Route::put('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'update'])->name('admin.surveys.update');
    
    // Survey Levels Management
    Route::resource('survey-levels', \App\Http\Controllers\Admin\SurveyLevelController::class)->names([
        'index' => 'admin.survey-levels.index',
        'create' => 'admin.survey-levels.create',
        'store' => 'admin.survey-levels.store',
        'show' => 'admin.survey-levels.show',
        'edit' => 'admin.survey-levels.edit',
        'update' => 'admin.survey-levels.update',
        'destroy' => 'admin.survey-levels.destroy',
    ]);
    
    // Survey Categories CMS
    Route::resource('survey-categories', \App\Http\Controllers\Admin\SurveyCategoryController::class)->names([
        'index' => 'admin.survey-categories.index',
        'create' => 'admin.survey-categories.create',
        'store' => 'admin.survey-categories.store',
        'show' => 'admin.survey-categories.show',
        'edit' => 'admin.survey-categories.edit',
        'update' => 'admin.survey-categories.update',
        'destroy' => 'admin.survey-categories.destroy',
    ]);
    Route::post('/survey-categories/{surveyCategory}/toggle-status', [\App\Http\Controllers\Admin\SurveyCategoryController::class, 'toggleStatus'])->name('admin.survey-categories.toggle-status');
    
    // Survey Levels CMS
    Route::post('/survey-levels/{surveyLevel}/toggle-status', [\App\Http\Controllers\Admin\SurveyLevelController::class, 'toggleStatus'])->name('admin.survey-levels.toggle-status');
    
    // Content Sections CMS
    Route::resource('content-sections', \App\Http\Controllers\Admin\ContentSectionController::class)->names([
        'index' => 'admin.content-sections.index',
        'create' => 'admin.content-sections.create',
        'store' => 'admin.content-sections.store',
        'show' => 'admin.content-sections.show',
        'edit' => 'admin.content-sections.edit',
        'update' => 'admin.content-sections.update',
        'destroy' => 'admin.content-sections.destroy',
    ]);
    Route::post('/content-sections/{contentSection}/toggle-status', [\App\Http\Controllers\Admin\ContentSectionController::class, 'toggleStatus'])->name('admin.content-sections.toggle-status');
    Route::get('/api/content-sections/subcategories', [\App\Http\Controllers\Admin\ContentSectionController::class, 'getSubcategories'])->name('admin.content-sections.get-subcategories');
    
    // Survey Section Assessments CMS
    Route::resource('survey-section-assessments', \App\Http\Controllers\Admin\SurveySectionAssessmentController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['survey-section-assessments' => 'assessment'])
        ->names([
            'index' => 'admin.survey-section-assessments.index',
            'show' => 'admin.survey-section-assessments.show',
            'edit' => 'admin.survey-section-assessments.edit',
            'update' => 'admin.survey-section-assessments.update',
            'destroy' => 'admin.survey-section-assessments.destroy',
        ]);
    Route::post('/survey-section-assessments/{assessment}/toggle-completion', [\App\Http\Controllers\Admin\SurveySectionAssessmentController::class, 'toggleCompletion'])->name('admin.survey-section-assessments.toggle-completion');
    Route::delete('/survey-section-assessments/{assessment}/delete-photo', [\App\Http\Controllers\Admin\SurveySectionAssessmentController::class, 'deletePhoto'])->name('admin.survey-section-assessments.delete-photo');
    
    // ============================================
    // SURVEY BUILDER - Wizard Style Admin Panel
    // ============================================
    
    // Survey Section Builder (Main Page)
    Route::get('/survey-builder', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'index'])->name('admin.survey-builder.index');
    
    // Accommodation Builder
    Route::get('/accommodation-builder', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'index'])->name('admin.accommodation-builder.index');
    
    // Global Options Manager
    Route::get('/survey-options', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'index'])->name('admin.survey-options.index');
    
    // ============================================
    // SURVEY BUILDER API ENDPOINTS
    // ============================================
    
    // Categories API
    Route::post('/api/categories', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'storeCategory']);
    Route::put('/api/categories/{category}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'updateCategory']);
    Route::delete('/api/categories/{category}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'deleteCategory']);
    Route::post('/api/categories/reorder', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'reorderCategories']);
    
    // Subcategories API
    Route::post('/api/subcategories', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'storeSubcategory']);
    Route::put('/api/subcategories/{subcategory}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'updateSubcategory']);
    Route::delete('/api/subcategories/{subcategory}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'deleteSubcategory']);
    Route::post('/api/subcategories/reorder', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'reorderSubcategories']);
    
    // Section Definitions API
    Route::get('/api/section-definitions/{section}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'getSection']);
    Route::post('/api/section-definitions', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'storeSection']);
    Route::put('/api/section-definitions/{section}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'updateSection']);
    Route::delete('/api/section-definitions/{section}', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'deleteSection']);
    Route::post('/api/section-definitions/{section}/clone', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'cloneSection']);
    Route::post('/api/section-definitions/reorder', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'reorderSections']);
    
    // Bulk Actions API
    Route::post('/api/bulk-action', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'bulkAction']);
    
    // Preview API
    Route::get('/api/preview', [\App\Http\Controllers\Admin\SurveyBuilderController::class, 'preview']);
    
    // ============================================
    // ACCOMMODATION BUILDER API ENDPOINTS
    // ============================================
    
    // Accommodation Types API
    Route::post('/api/accommodation-types', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'storeType']);
    Route::get('/api/accommodation-types/{type}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'getType']);
    Route::put('/api/accommodation-types/{type}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'updateType']);
    Route::delete('/api/accommodation-types/{type}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'deleteType']);
    Route::post('/api/accommodation-types/reorder', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'reorderTypes']);
    Route::post('/api/accommodation-types/{type}/clone', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'cloneType']);
    Route::get('/api/accommodation-types/{type}/components', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'getTypeComponents']);
    Route::post('/api/accommodation-types/{type}/components', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'updateTypeComponents']);
    
    // Accommodation Components API
    Route::post('/api/accommodation-components', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'storeComponent']);
    Route::put('/api/accommodation-components/{component}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'updateComponent']);
    Route::delete('/api/accommodation-components/{component}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'deleteComponent']);
    Route::post('/api/accommodation-components/reorder', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'reorderComponents']);
    
    // Accommodation Options API (Materials & Defects)
    Route::post('/api/accommodation-options', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'storeOption']);
    Route::put('/api/accommodation-options/{accommodationOption}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'updateOption']);
    Route::delete('/api/accommodation-options/{accommodationOption}', [\App\Http\Controllers\Admin\AccommodationBuilderController::class, 'deleteOption']);
    
    // ============================================
    // GLOBAL OPTIONS API ENDPOINTS
    // ============================================
    
    // Option Types API
    Route::post('/api/option-types', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'storeOptionType']);
    Route::put('/api/option-types/{optionType}', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'updateOptionType']);
    Route::delete('/api/option-types/{optionType}', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'deleteOptionType']);
    
    // Options API
    Route::post('/api/options', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'storeOption']);
    Route::put('/api/options/{option}', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'updateOption']);
    Route::delete('/api/options/{option}', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'deleteOption']);
    Route::post('/api/options/reorder', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'reorderOptions']);
    Route::post('/api/options/bulk-delete', [\App\Http\Controllers\Admin\GlobalOptionsController::class, 'bulkDeleteOptions']);
});

// Surveyor Routes
Route::prefix('surveyor')->middleware(['auth', 'surveyor'])->group(function () {
    Route::get('/dashboard', [SurveyorDashboard::class, 'index'])->name('surveyor.dashboard');
    
    // Surveys
    Route::get('/surveys', [\App\Http\Controllers\Surveyor\SurveyController::class, 'index'])->name('surveyor.surveys.index');
    // Mock routes - require survey parameter
    Route::get('/surveys/{survey}/detail-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'detailMock'])->name('surveyor.surveys.detail.mock');
    Route::get('/surveys/{survey}/desk-study-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deskStudyMock'])->name('surveyor.surveys.desk-study.mock');
    Route::get('/surveys/{survey}/data-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'dataMock'])->name('surveyor.surveys.data.mock');
    Route::get('/surveys/{survey}/generate-pdf', [\App\Http\Controllers\Surveyor\SurveyController::class, 'generatePdfReport'])->name('surveyor.surveys.generate-pdf');
    Route::post('/surveys/{survey}/sections/{sectionDefinition}/save', [\App\Http\Controllers\Surveyor\SurveyController::class, 'saveSectionAssessment'])->name('surveyor.surveys.sections.save');
    Route::post('/surveys/{survey}/accommodations/save', [\App\Http\Controllers\Surveyor\SurveyController::class, 'saveAccommodationAssessment'])->name('surveyor.surveys.accommodations.save');
    Route::post('/surveys/{survey}/assessments/{assessment}/condition-rating', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateConditionRating'])->name('surveyor.surveys.assessments.update-condition-rating');
    Route::post('/surveys/{survey}/assessments/{assessment}/costs', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateCosts'])->name('surveyor.surveys.assessments.update-costs');
    Route::post('/surveys/{survey}/assessments/{assessment}/photos', [\App\Http\Controllers\Surveyor\SurveyController::class, 'uploadPhotos'])->name('surveyor.surveys.assessments.upload-photos');
    Route::post('/surveys/{survey}/assessments/{assessment}/photos/{photo}/delete', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deletePhoto'])->name('surveyor.surveys.delete-photo');
    Route::post('/surveys/{survey}/accommodation-assessments/{assessment}/photos', [\App\Http\Controllers\Surveyor\SurveyController::class, 'uploadAccommodationPhotos'])->name('surveyor.surveys.accommodation-assessments.upload-photos');
    Route::post('/surveys/{survey}/accommodation-assessments/{assessment}/photos/{photo}/delete', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deleteAccommodationPhoto'])->name('surveyor.surveys.delete-accommodation-photo');
    Route::post('/surveys/{survey}/clone-section-item', [\App\Http\Controllers\Surveyor\SurveyController::class, 'cloneSectionItem'])->name('surveyor.surveys.clone-section-item');
    Route::post('/surveys/{survey}/clone-accommodation-item', [\App\Http\Controllers\Surveyor\SurveyController::class, 'cloneAccommodationItem'])->name('surveyor.surveys.clone-accommodation-item');
    Route::post('/surveys/{survey}/content-sections/{contentSection}/update', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateContentSection'])->name('surveyor.surveys.content-sections.update');
    Route::get('/surveys/{survey}/media-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'mediaMock'])->name('surveyor.surveys.media.mock');
    Route::get('/surveys/{survey}/transcript-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'transcriptMock'])->name('surveyor.surveys.transcript.mock');
    Route::get('/surveys/{survey}/documents-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'documentsMock'])->name('surveyor.surveys.documents.mock');
    Route::post('/surveys/{survey}/status', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateStatus'])->name('surveyor.surveys.updateStatus');
    Route::post('/surveys/{survey}/claim', [\App\Http\Controllers\Surveyor\SurveyController::class, 'claim'])->name('surveyor.surveys.claim');

    Route::post('new-survey/store', [SurveyController::class, 'createNewSurvey'])->name('surveyor.surveys.createNewSurvey');

    Route::get('survey/details/{id}', [SurveyController::class, 'surveyDetails'])->name('surveyor.surveys.surveyDetails');
    Route::post('survey/update', [SurveyController::class, 'updateSurvey'])->name('surveyor.surveys.updateSurvey');
    Route::post('survey/note/add', [SurveyController::class, 'addSurveyNote'])->name('surveyor.surveys.addSurveyNote');
    
    // aws
    Route::post('/aws-transcription', [AwsTranscriptionController::class, 'uploadMedia'])->name('aws.transcription');  
});

// Client Routes
Route::prefix('client')->middleware(['auth', 'client'])->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('client.dashboard');
    
    // Surveys
    Route::get('/surveys', [\App\Http\Controllers\Client\SurveyController::class, 'index'])->name('client.surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Client\SurveyController::class, 'show'])->name('client.surveys.show');
    Route::get('/surveys/{survey}/report', [\App\Http\Controllers\Client\SurveyController::class, 'showReport'])->name('client.surveys.report');
    Route::get('/surveys/{survey}/download-pdf', [\App\Http\Controllers\Client\SurveyController::class, 'downloadPdf'])->name('client.surveys.download-pdf');
});

