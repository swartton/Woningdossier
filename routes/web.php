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

            // my account
            Route::group(['as' => 'my-account.', 'prefix' => 'my-account', 'namespace' => 'MyAccount', 'middleware' => 'deny-if-filling-for-other-building'], function () {
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
            });

            // conversation requests
            Route::group(['prefix' => 'conversation-request', 'as' => 'conversation-requests.', 'namespace' => 'ConversationRequest', 'middleware' => 'can:view-any,App\Models\PrivateMessage'], function () {
                Route::get('{requestType}/{measureApplicationShort?}', 'ConversationRequestController@index')->name('index');
                Route::post('', 'ConversationRequestController@store')->name('store');
            });

            // the tool
            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::post('', 'ImportController@copy')->name('copy');
            });

            Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], function () {
                Route::get('/', 'ToolController@index')->name('index');

                Route::group(['prefix' => 'questionnaire', 'as' => 'questionnaire.'], function () {
                    Route::post('', 'QuestionnaireController@store')->name('store');
                });

                Route::resource('example-building', 'ExampleBuildingController')->only('store');
                Route::resource('building-type', 'BuildingTypeController')->only('store');

                Route::group(['as' => 'general-data.', 'prefix' => 'general-data'], function () {
                    Route::get('', 'GeneralDataController@index')->name('index');

                    Route::group(['namespace' => 'GeneralData'], function () {
                        Route::resource('gebouw-kenmerken', 'BuildingCharacteristicsController')->only(['index', 'store'])->names('building-characteristics');
                        Route::get('get-qualified-example-buildings', 'BuildingCharacteristicsController@qualifiedExampleBuildings')->name('building-characteristics.qualified-example-buildings');

                        Route::resource('huidige-staat', 'CurrentStateController')->names('current-state')->only(['index', 'store']);
                        Route::resource('gebruik', 'UsageController')->only(['index', 'store'])->names('usage');
                        Route::resource('interesse', 'InterestController')->only(['index', 'store'])->names('interest');
                    });
                });

                Route::group(['middleware' => 'filled-step:general-data'], function () {
                    // Heat pump: info for now
                    Route::resource('heat-pump', 'HeatPumpController', ['only' => ['index', 'store']])
                        ->middleware('step-disabled:heat-pump');

                    Route::group(['prefix' => 'ventilation', 'as' => 'ventilation.', 'middleware' => 'step-disabled:ventilation'], function () {
                        Route::resource('', 'VentilationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'VentilationController@calculate')->name('calculate');
                    });

                    // Wall Insulation
                    Route::group(['prefix' => 'wall-insulation', 'as' => 'wall-insulation.', 'middleware' => 'step-disabled:wall-insulation'], function () {
                        Route::resource('', 'WallInsulationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'WallInsulationController@calculate')->name('calculate');
                    });

                    // Insulated glazing
                    Route::group(['prefix' => 'insulated-glazing', 'as' => 'insulated-glazing.', 'middleware' => 'step-disabled:insulated-glazing'], function () {
                        Route::resource('', 'InsulatedGlazingController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'InsulatedGlazingController@calculate')->name('calculate');
                    });

                    // Floor Insulation
                    Route::group(['prefix' => 'floor-insulation', 'as' => 'floor-insulation.', 'middleware' => 'step-disabled:insulated-glazing'], function () {
                        Route::resource('', 'FloorInsulationController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'FloorInsulationController@calculate')->name('calculate');
                    });

                    // Roof Insulation
                    Route::group(['prefix' => 'roof-insulation', 'as' => 'roof-insulation.', 'middleware' => 'step-disabled:roof-insulation'], function () {
                        Route::resource('', 'RoofInsulationController');
                        Route::post('calculate', 'RoofInsulationController@calculate')->name('calculate');
                    });

                    // HR boiler
                    Route::group(['prefix' => 'high-efficiency-boiler', 'as' => 'high-efficiency-boiler.', 'middleware' => 'step-disabled:high-efficiency-boiler'], function () {
                        Route::resource('', 'HighEfficiencyBoilerController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'HighEfficiencyBoilerController@calculate')->name('calculate');
                    });

                    // Solar panels
                    Route::group(['prefix' => 'solar-panels', 'as' => 'solar-panels.', 'middleware' => 'step-disabled:solar-panels'], function () {
                        Route::resource('', 'SolarPanelsController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'SolarPanelsController@calculate')->name('calculate');
                    });

                    // Heater (solar boiler)
                    Route::group(['prefix' => 'heater', 'as' => 'heater.', 'middleware' => 'step-disabled:heater'], function () {
                        Route::resource('', 'HeaterController', ['only' => ['index', 'store']]);
                        Route::post('calculate', 'HeaterController@calculate')->name('calculate');
                    });
                });

                Route::get('my-plan', 'MyPlanController@index')->name('my-plan.index');
                Route::post('my-plan/comment', 'MyPlanController@storeComment')
                    ->middleware('deny-if-observing-building')
                    ->name('my-plan.store-comment');
                Route::post('my-plan/store', 'MyPlanController@store')->name('my-plan.store');
//                Route::get('my-plan/export', 'MyPlanController@export')->name('my-plan.export');
            });

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
