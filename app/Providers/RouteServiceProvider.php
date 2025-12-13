<?php

namespace App\Providers;

use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use App\Models\SurveySectionDefinition;
use App\Models\SurveyOptionType;
use App\Models\SurveyOption;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyContentSection;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/admin/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Route model bindings for Survey Builder
        Route::model('category', SurveyCategory::class);
        Route::model('subcategory', SurveySubcategory::class);
        Route::model('section', SurveySectionDefinition::class);
        Route::model('optionType', SurveyOptionType::class);
        Route::model('option', SurveyOption::class);
        Route::model('type', SurveyAccommodationType::class);
        Route::model('component', SurveyAccommodationComponent::class);
        Route::model('accommodationOption', SurveyAccommodationOption::class);
        Route::model('contentSection', SurveyContentSection::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}











