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

				Route::get('cooperations', 'CooperationsController@index')->name('cooperations.index');
			});

            Route::group(['prefix' => 'tools', 'as' => 'tools.', 'namespace' => 'Tools'], function () {
                Route::resource('general-data', 'GeneralDataController');
            });

		});


	});

});

Route::get('/', function () {
	return view('welcome');
})->name('index');