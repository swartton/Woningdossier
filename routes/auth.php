<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here are all auth related routes.
| Namespace: Auth
|
|
*/

Route::get('check-existing-mail', 'RegisterController@checkExistingEmail')->name('check-existing-email');
Route::post('connect-existing-account', 'RegisterController@connectExistingAccount')->name('connect-existing-account');

Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'RegisterController@register');

Route::group(['as' => 'auth.'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');

    Route::post('logout', 'LoginController@logout')->name('logout');

    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('request', 'ForgotPasswordController@index')->name('request.index');
        Route::post('request', 'ForgotPasswordController@store')->name('request.store');

        Route::get('reset/{token}/{email}', 'ResetPasswordController@show')->name('reset.show');
        Route::post('reset', 'ResetPasswordController@update')->name('reset.update');
    });

    Route::group(['prefix' => 'confirm', 'as' => 'confirm.'], function () {
        Route::get('', 'ConfirmAccountController@store')->name('store');

        Route::group(['prefix' => 'resend', 'as' => 'resend.'], function () {
            Route::get('', 'ResendConfirmAccountController@show')->name('show');
            Route::post('', 'ResendConfirmAccountController@store')->name('store');
        });
    });
});