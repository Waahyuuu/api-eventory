<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Where to redirect users after login.
     */
    public const HOME = '/home';

    /**
     * Bootstrap route services.
     */
    public function boot(): void
    {
        $this->routes(function () {
            
            // === ENABLE API ROUTES ===
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // === DEFAULT WEB ROUTES ===
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
