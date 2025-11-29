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
    // Mock routes - require survey parameter
    Route::get('/surveys/{survey}/detail-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'detailMock'])->name('surveyor.surveys.detail.mock');
    Route::get('/surveys/{survey}/desk-study-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deskStudyMock'])->name('surveyor.surveys.desk-study.mock');
    Route::get('/surveys/{survey}/data-mock', [\App\Http\Controllers\Surveyor\SurveyController::class, 'dataMock'])->name('surveyor.surveys.data.mock');
    Route::post('/surveys/{survey}/sections/{sectionDefinition}/save', [\App\Http\Controllers\Surveyor\SurveyController::class, 'saveSectionAssessment'])->name('surveyor.surveys.sections.save');
    Route::post('/surveys/{survey}/accommodations/save', [\App\Http\Controllers\Surveyor\SurveyController::class, 'saveAccommodationAssessment'])->name('surveyor.surveys.accommodations.save');
    Route::post('/surveys/{survey}/assessments/{assessment}/condition-rating', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateConditionRating'])->name('surveyor.surveys.assessments.update-condition-rating');
    Route::post('/surveys/{survey}/assessments/{assessment}/costs', [\App\Http\Controllers\Surveyor\SurveyController::class, 'updateCosts'])->name('surveyor.surveys.assessments.update-costs');
    Route::post('/surveys/{survey}/assessments/{assessment}/photos', [\App\Http\Controllers\Surveyor\SurveyController::class, 'uploadPhotos'])->name('surveyor.surveys.assessments.upload-photos');
    Route::post('/surveys/{survey}/assessments/{assessment}/photos/{photo}/delete', [\App\Http\Controllers\Surveyor\SurveyController::class, 'deletePhoto'])->name('surveyor.surveys.delete-photo');
    Route::post('/surveys/{survey}/clone-section-item', [\App\Http\Controllers\Surveyor\SurveyController::class, 'cloneSectionItem'])->name('surveyor.surveys.clone-section-item');
    Route::post('/surveys/{survey}/clone-accommodation-item', [\App\Http\Controllers\Surveyor\SurveyController::class, 'cloneAccommodationItem'])->name('surveyor.surveys.clone-accommodation-item');
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
});

