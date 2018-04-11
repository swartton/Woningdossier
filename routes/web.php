<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::domain('{cooperation}.' . config('woningdossier.domain'))->group(function(){

	Route::group(['middleware' => 'cooperation', 'as' => 'cooperation.', 'namespace' => 'Cooperation'], function() {
		Route::get('/', function() { return view( 'cooperation.welcome' ); })->name('welcome');
		Route::get('switch-language/{locale}', 'UserLanguageController@switchLanguage')->name('switch-language');
		Route::get( 'confirm',
            'Auth\RegisterController@confirm' )->name( 'confirm' );

		Route::get('fill-address', 'Auth\RegisterController@fillAddress')->name('fill-address');
		// Login, forgot password etc.
		Auth::routes();


		// Logged In Section
		Route::group(['middleware' => 'auth'], function(){
			Route::get( 'home', 'HomeController@index' )->name( 'home' );

			Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount'], function() {
				Route::resource('settings', 'SettingsController', ['only' => ['index', 'store', ]]);
				Route::delete('settings', 'SettingsController@destroy')->name('settings.destroy');

				//Route::get('cooperations', 'CooperationsController@index')->name('cooperations.index');
			});

            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], function () {
            	Route::get('/', 'ToolController@index')->name('index');
                Route::resource('general-data', 'GeneralDataController', ['only' => ['index', 'store']]);

                Route::group(['middleware' => 'filled-step:general-data'], function(){
                	// Wall Insulation
	                Route::resource('wall-insulation', 'WallInsulationController', ['only' => ['index', 'store']]);
	                Route::post('wall-insulation/calculate', 'WallInsulationController@calculate')->name('wall-insulation.calculate');

	                // Insulated glazing
	                Route::resource('insulated-glazing', 'InsulatedGlazingController', ['only' => ['index', 'store']]);
	                Route::post('insulated-glazing/calculate', 'InsulatedGlazingController@calculate')->name('insulated-glazing.calculate');

	                // Floor Insulation
	                Route::resource('floor-insulation', 'FloorInsulationController', ['only' => ['index', 'store']]);
	                Route::post('floor-insulation/calculate', 'FloorInsulationController@calculate')->name('floor-insulation.calculate');

                });


	            Route::resource('roof-insulation', 'RoofInsulationController');
				Route::resource('high-efficiency-boiler', 'HighEfficiencyBoilerController');
				Route::resource('heat-pump', 'HeatPumpController');
				Route::resource('solar-panels', 'SolarPanelsController');
				Route::resource('heater', 'HeaterController');
            });

		});


	});

});

Route::get('/', function () {
	return view('welcome');
})->name('index');