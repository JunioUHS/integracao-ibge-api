<?php

use App\Http\Controllers\IntegracaoIbgeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('ibge')->group(function () {
        Route::get('cidades', [IntegracaoIbgeController::class, 'getCitiesByState']);
        Route::get('populacao/{locationId}/{year}', [IntegracaoIbgeController::class, 'getPopulation'])
            ->where(['locationId' => '[0-9]+', 'year' => '[0-9]{4}']);
    });
});