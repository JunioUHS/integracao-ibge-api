<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Rest\RestService;
use App\Repositories\IbgeRepository;
use App\Services\IbgeIntegrator\IbgeIntegrationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RestService::class, function () {
            return new RestService();
        });

        $this->app->bind(IbgeRepository::class, function ($app) {
            return new IbgeRepository($app->make(RestService::class));
        });

        $this->app->bind(IbgeIntegrationService::class, function ($app) {
            return new IbgeIntegrationService($app->make(IbgeRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
