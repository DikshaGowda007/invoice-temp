<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $apiv1Namespace = 'App\Http\Controllers\Api\V1';

    public function register(): void
    {
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api/v1')->namespace($this->apiv1Namespace)->group(base_path('routes/api_v1.php'));
    }
}
