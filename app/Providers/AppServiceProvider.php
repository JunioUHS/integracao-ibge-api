<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Log::info('AppServiceProvider::register called');
        
        try {
            // Registrar HttpService
            $this->app->singleton(\App\Services\Rest\HttpService::class, function ($app) {
                Log::info('Creating HttpService instance');
                return new \App\Services\Rest\HttpService();
            });

            // Registrar IbgeRepository
            $this->app->singleton(\App\Repositories\IbgeRepository::class, function ($app) {
                Log::info('Creating IbgeRepository instance');
                return new \App\Repositories\IbgeRepository($app->make(\App\Services\Rest\HttpService::class));
            });

            // Registrar IbgeIntegrationService
            $this->app->singleton(\App\Services\IbgeIntegrator\IbgeIntegrationService::class, function ($app) {
                Log::info('Creating IbgeIntegrationService instance');
                return new \App\Services\IbgeIntegrator\IbgeIntegrationService($app->make(\App\Repositories\IbgeRepository::class));
            });
            
            Log::info('All services registered successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in AppServiceProvider::register', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Log::info('AppServiceProvider::boot called');
    }
}
