<?php

namespace App\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

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
    public function boot(ResponseFactory $factory)
    {
        /**
         * @param array|object $data
         * @param int $status
         * @param string $message
         * @return \Illuminate\Http\Response
         */
        $factory->macro(
            'jsonResponse', function ($data = [],  int $status = 200) use ($factory) {

            $customFormat = [
                'status' => $status,
                'message' => '',
                'body' => $data
            ];
            return $factory->make($customFormat, $status);
        });
    }
}
