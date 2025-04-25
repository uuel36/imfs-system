<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Models\Warehouse\RequestOrder;
use App\Models\Warehouse\Item; // Add the Item model
use App\Models\HR\Area; // Add the Area model
use App\Http\Controllers\Company\CompanyController;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    public const COMPANY = '/company';

    public const USER = '/user';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        view()->composer('*', function ($view) {
            $requestOrders = RequestOrder::all();
            $itemNames = Item::pluck('name', 'id');
            $areaNames = Area::pluck('name', 'id');

            // Fetch leadman names using the CompanyController
            $companyController = app(CompanyController::class);
            $leadmanNames = $companyController->getLeadmanNames();

            $view->with([
                'requestOrders' => $requestOrders,
                'itemNames' => $itemNames,
                'areaNames' => $areaNames,
                'leadmanNames' => $leadmanNames, // Add leadman names to the view data
            ]);
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
