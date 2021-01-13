<?php

/*
|--------------------------------------------------------------------------
| My Account Routes
|--------------------------------------------------------------------------
|
| Here are all my account related routes.
| Namespace: MyAccount
| Prefix: my-account
| As: my-account.
*/

Route::get('', 'MyAccountController@index')->name('index');

Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::put('', 'SettingsController@update')->name('update');
    Route::delete('destroy', 'SettingsController@destroy')->name('destroy');
    Route::post('reset-dossier', 'SettingsController@resetFile')->name('reset-file');
});

Route::resource('hoom-settings', 'HoomSettingsController');

Route::group(['as' => 'import-center.', 'prefix' => 'import-centrum'], function () {
    Route::get('set-compare-session/{inputSourceShort}', 'ImportCenterController@setCompareSession')->name('set-compare-session');
    Route::post('dismiss-notification', 'ImportCenterController@dismissNotification')->name('dismiss-notification');
});

Route::resource('notification-settings', 'NotificationSettingsController')->only([
    'index', 'show', 'update',
]);

Route::group(['as' => 'messages.', 'prefix' => 'messages'], function () {
    Route::get('', 'MessagesController@index')->name('index');
    Route::get('edit', 'MessagesController@edit')->name('edit');
    Route::post('edit', 'MessagesController@store')->name('store');
});

// the checkbox to deny the whole access for everyone.
Route::post('access/allow-access', 'AccessController@allowAccess')->name('access.allow-access');