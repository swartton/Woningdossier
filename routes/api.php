<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\RegisterController;

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

Route::domain('{cooperation}.' . config('hoomdossier.domain'))
    ->middleware(['auth:sanctum', 'cooperation'])->as('api.cooperation.')
    ->namespace('Cooperation')
    ->group(function () {

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        });
    });

Route::group(['namespace' => 'Api'], function () {
    Route::get('address-data', 'GeoController@getAddressData');
});

