<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Surveyor\DashboardController as SurveyorDashboard;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
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
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    
    // Survey Management
    Route::get('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('admin.surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'show'])->name('admin.surveys.show');
    Route::get('/surveys/{survey}/edit', [\App\Http\Controllers\Admin\SurveyController::class, 'edit'])->name('admin.surveys.edit');
    Route::put('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'update'])->name('admin.surveys.update');
    
    // Survey Sections (Admin)
    Route::get('/survey/{survey}/sections', [\App\Http\Controllers\Admin\SurveySectionController::class, 'showSections'])->name('admin.survey.sections');
    
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
    Route::get('/survey/{survey}/section/{section}', [\App\Http\Controllers\Admin\SurveySectionController::class, 'showSectionAssessment'])->name('admin.survey.section.assessment');
    
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
    
    // Survey Sections CMS
    Route::resource('survey-sections', \App\Http\Controllers\Admin\SurveySectionController::class)->names([
        'index' => 'admin.survey-sections.index',
        'create' => 'admin.survey-sections.create',
        'store' => 'admin.survey-sections.store',
        'show' => 'admin.survey-sections.show',
        'edit' => 'admin.survey-sections.edit',
        'update' => 'admin.survey-sections.update',
        'destroy' => 'admin.survey-sections.destroy',
    ]);
    Route::post('/survey-sections/{surveySection}/toggle-status', [\App\Http\Controllers\Admin\SurveySectionController::class, 'toggleStatus'])->name('admin.survey-sections.toggle-status');
    
    // Section Fields Management
    Route::get('/survey-sections/{surveySection}/fields/{field}', [\App\Http\Controllers\Admin\SurveySectionController::class, 'showField'])->name('admin.survey-sections.fields.show');
    Route::post('/survey-sections/{surveySection}/fields', [\App\Http\Controllers\Admin\SurveySectionController::class, 'storeField'])->name('admin.survey-sections.fields.store');
    Route::put('/survey-sections/{surveySection}/fields/{field}', [\App\Http\Controllers\Admin\SurveySectionController::class, 'updateField'])->name('admin.survey-sections.fields.update');
    Route::delete('/survey-sections/{surveySection}/fields/{field}', [\App\Http\Controllers\Admin\SurveySectionController::class, 'deleteField'])->name('admin.survey-sections.fields.delete');
    Route::post('/survey-sections/{surveySection}/fields/reorder', [\App\Http\Controllers\Admin\SurveySectionController::class, 'reorderFields'])->name('admin.survey-sections.fields.reorder');
    
    // Survey Levels CMS (update existing routes)
    Route::post('/survey-levels/{surveyLevel}/toggle-status', [\App\Http\Controllers\Admin\SurveyLevelController::class, 'toggleStatus'])->name('admin.survey-levels.toggle-status');
    
    // Survey Section Assessments CMS
    Route::resource('survey-section-assessments', \App\Http\Controllers\Admin\SurveySectionAssessmentController::class)->only(['index', 'show', 'edit', 'update', 'destroy'])->names([
        'index' => 'admin.survey-section-assessments.index',
        'show' => 'admin.survey-section-assessments.show',
        'edit' => 'admin.survey-section-assessments.edit',
        'update' => 'admin.survey-section-assessments.update',
        'destroy' => 'admin.survey-section-assessments.destroy',
    ]);
    Route::post('/survey-section-assessments/{assessment}/toggle-completion', [\App\Http\Controllers\Admin\SurveySectionAssessmentController::class, 'toggleCompletion'])->name('admin.survey-section-assessments.toggle-completion');
    Route::delete('/survey-section-assessments/{assessment}/delete-photo', [\App\Http\Controllers\Admin\SurveySectionAssessmentController::class, 'deletePhoto'])->name('admin.survey-section-assessments.delete-photo');
});

// Surveyor Routes
Route::prefix('surveyor')->middleware(['auth', 'surveyor'])->group(function () {
    Route::get('/dashboard', [SurveyorDashboard::class, 'index'])->name('surveyor.dashboard');
    
    // Surveys
    Route::get('/surveys', [\App\Http\Controllers\Surveyor\SurveyController::class, 'index'])->name('surveyor.surveys.index');
    Route::get('/surveys/detail-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'detailMock'])->name('surveyor.surveys.detail.mock');
    Route::get('/surveys/desk-study-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deskStudyMock'])->name('surveyor.surveys.desk-study.mock');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Surveyor\SurveyController::class, 'show'])->name('surveyor.surveys.show');
    // Temporary route for new detail design
    Route::get('/surveys/{survey}/detail', [\App\Http\Controllers\Surveyor\SurveyController::class, 'detail'])->name('surveyor.surveys.detail');
    Route::post('/surveys/{survey}/status', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateStatus'])->name('surveyor.surveys.updateStatus');
    Route::post('/surveys/{survey}/claim', [\App\Http\Controllers\Surveyor\SurveyController::class, 'claim'])->name('surveyor.surveys.claim');
    Route::post('/surveys/{survey}/start', [\App\Http\Controllers\Surveyor\SurveyController::class, 'start'])->name('surveyor.surveys.start');

    Route::post('new-survey/store', [SurveyController::class, 'createNewSurvey'])->name('surveyor.surveys.createNewSurvey');
    
    // Survey Sections
    Route::get('/survey/{survey}/categories', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'showCategories'])->name('surveyor.survey.categories');
    Route::get('/survey/{survey}/category/{category}/sections', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'showCategorySections'])->name('surveyor.survey.category.sections');
    Route::get('/survey/{survey}/sections', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'showSections'])->name('surveyor.survey.sections');
    Route::get('/survey/{survey}/section/{section}', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'showSectionForm'])->name('surveyor.survey.section.form');
    Route::post('/survey/{survey}/section/{section}', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'saveSectionAssessment'])->name('surveyor.survey.section.save');
    Route::delete('/survey/{survey}/section/{section}/photo', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'deletePhoto'])->name('surveyor.survey.section.deletePhoto');
    Route::post('/survey/{survey}/section/{section}/incomplete', [\App\Http\Controllers\Surveyor\SurveySectionController::class, 'markIncomplete'])->name('surveyor.survey.section.incomplete');
    
    // Survey Media
    Route::get('/survey/{survey}/media', [\App\Http\Controllers\Surveyor\MediaController::class, 'index'])->name('surveyor.survey.media');
    Route::post('/survey/{survey}/media/upload', [\App\Http\Controllers\Surveyor\MediaController::class, 'upload'])->name('surveyor.survey.media.upload');
    Route::delete('/survey/{survey}/media/delete', [\App\Http\Controllers\Surveyor\MediaController::class, 'delete'])->name('surveyor.survey.media.delete');
    Route::get('/survey/{survey}/media/list', [\App\Http\Controllers\Surveyor\MediaController::class, 'getMedia'])->name('surveyor.survey.media.list');
});

// Client Routes
Route::prefix('client')->middleware(['auth', 'client'])->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('client.dashboard');
    
    // Surveys
    Route::get('/surveys', [\App\Http\Controllers\Client\SurveyController::class, 'index'])->name('client.surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Client\SurveyController::class, 'show'])->name('client.surveys.show');
});

