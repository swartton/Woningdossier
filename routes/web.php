<?php

/** @noinspection PhpParamsInspection */

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

Route::domain('{cooperation}.'.config('hoomdossier.domain'))->group(function () {
    Route::group(['middleware' => 'cooperation', 'as' => 'cooperation.', 'namespace' => 'Cooperation'], function () {
        if ('local' == app()->environment()) {
            Route::get('mail', function () {
//            return new \App\Mail\UserCreatedEmail(\App\Models\Cooperation::find(1), \App\Models\User::find(1), 'sdfkhasgdfuiasdgfyu');
//            return new \App\Mail\UserAssociatedWithCooperation(\App\Models\Cooperation::find(1), \App\Models\User::find(1));
//            return new \App\Mail\UserChangedHisEmail(\App\Models\User::find(1), \App\Models\Account::find(1), 'demo@eg.com', 'bier@pils.com');
                return new  \App\Mail\UnreadMessagesEmail(\App\Models\User::find(1), \App\Models\Cooperation::find(1), 10);
//            return new \App\Mail\ResetPasswordRequest(\App\Models\Cooperation::find(1), \App\Models\Account::find(1), 'sfklhasdjkfhsjkf');
//            return new \App\Mail\RequestAccountConfirmationEmail(\App\Models\User::find(1), \App\Models\Cooperation::find(1));
            });
        }

        Route::get('/', function () {
            return view('cooperation.welcome');
        })->name('welcome');

        Route::get('switch-language/{locale}', 'UserLanguageController@switchLanguage')->name('switch-language');

        // Auth routes
        Route::group(['namespace' => 'Auth'], base_path('routes/auth.php'));

        Route::group(['prefix' => 'create-building', 'as' => 'create-building.'], function () {
            Route::get('', 'CreateBuildingController@index')->name('index');
            Route::post('', 'CreateBuildingController@store')->name('store');
        });

        Route::group(['as' => 'recover-old-email.', 'prefix' => 'recover-old-email'], function () {
            Route::get('{token}', 'RecoverOldEmailController@recover')->name('recover');
        });

        // group can be accessed by everyone that's authorized and has a role in its session
        Route::group(['middleware' => ['auth', 'current-role:resident|cooperation-admin|coordinator|coach|super-admin|superuser']], function () {
            Route::get('messages/count', 'MessagesController@getTotalUnreadMessageCount')->name('message.get-total-unread-message-count');
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');

            if ('local' == app()->environment()) {
                // debug purpose only
                Route::group(['as' => 'pdf.', 'namespace' => 'Pdf', 'prefix' => 'pdf'], function () {
                    Route::group(['as' => 'user-report.', 'prefix' => 'user-report'], function () {
                        Route::get('', 'UserReportController@index')->name('index');
                    });
                });
            }
            Route::get('home', 'HomeController@index')->name('home')->middleware('deny-if-filling-for-other-building');

            Route::resource('privacy', 'PrivacyController')->only('index');
            Route::resource('disclaimer', 'DisclaimController')->only('index');

            Route::group(['prefix' => 'file-storage', 'as' => 'file-storage.'], function () {
                Route::post('{fileType}', 'FileStorageController@store')
                    ->name('store');
                Route::get('is-being-processed/{fileType}', 'FileStorageController@checkIfFileIsBeingProcessed')->name('check-if-file-is-being-processed');

                Route::get('download/{fileStorage}', 'FileStorageController@download')
                    ->name('download');
            });

            Route::get('input-source/{input_source_value_id}', 'InputSourceController@changeInputSourceValue')->name('input-source.change-input-source-value');

            Route::group(['as' => 'messages.', 'prefix' => 'messages', 'namespace' => 'Messages'], function () {
                Route::group(['as' => 'participants.', 'prefix' => 'participants'], function () {
                    Route::post('revoke-access', 'ParticipantController@revokeAccess')->name('revoke-access');

                    Route::post('add-with-building-access', 'ParticipantController@addWithBuildingAccess')->name('add-with-building-access');

                    Route::post('set-read', 'ParticipantController@setRead')->name('set-read');
                });
            });

            // My Account routes
            Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount', 'middleware' => 'deny-if-filling-for-other-building'], base_path('routes/my-account.php'));

            // conversation requests
            Route::group(['prefix' => 'conversation-request', 'as' => 'conversation-requests.', 'namespace' => 'ConversationRequest', 'middleware' => 'can:view-any,App\Models\PrivateMessage'], function () {
                Route::get('{requestType}/{measureApplicationShort?}', 'ConversationRequestController@index')->name('index');
                Route::post('', 'ConversationRequestController@store')->name('store');
            });

            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::post('', 'ImportController@copy')->name('copy');
            });

            // Tool routes
            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], base_path('routes/tool.php'));

            // Admin routes
            Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin',
                'middleware' => [
                    'role:cooperation-admin|coordinator|coach|super-admin|superuser',
                    'restore-building-session-if-filling-for-other-building',
                ],
            ], base_path('routes/admin.php'));
        });
    });
});

Route::get('/', function () {
    if (stristr(\Request::url(), '://www.')) {
        // The user has prefixed the subdomain with a www subdomain.
        // Remove the www part and redirect to that.
        return redirect(str_replace('://www.', '://', Request::url()));
    }

    return view('welcome');
})->name('index');
