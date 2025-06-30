<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @param array|object $data
         * @param int $status
         * @param string $message
         * @return \Illuminate\Http\JsonResponse
         */
        Response::macro('jsonResponse', function ($data = [], int $status = 200, string $message = '') {
            $customFormat = [
                'status' => $status,
                'message' => $message,
                'body' => $data
            ];
            
            return response()->json($customFormat, $status);
        });
    }
}
