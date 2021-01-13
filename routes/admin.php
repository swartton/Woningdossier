<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all admin related routes.
| Namespace: Admin
| Prefix: admin
| As: admin.
*/

Route::get('/', 'AdminController@index')->name('index');
Route::get('stop-session', 'AdminController@stopSession')->name('stop-session');
Route::get('/switch-role/{role}', 'SwitchRoleController@switchRole')->name('switch-role');

Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
    Route::post('assign-role', 'RoleController@assignRole')->name('assign-role');
    Route::post('remove-role', 'RoleController@removeRole')->name('remove-role');
});

Route::group(['middleware' => ['current-role:cooperation-admin|super-admin']], function () {
    Route::resource('example-buildings', 'ExampleBuildingController');
    Route::get('example-buildings/{id}/copy', 'ExampleBuildingController@copy')->name('example-buildings.copy');
});

/* Section that a coach, coordinator and cooperation-admin can access */
Route::group(['middleware' => ['current-role:cooperation-admin|coach|coordinator']], function () {
    Route::resource('messages', 'MessagesController')->only('index');

    Route::group(['prefix' => 'tool', 'as' => 'tool.'], function () {
        Route::get('fill-for-user/{id}', 'ToolController@fillForUser')->name('fill-for-user');
        Route::get('observe-tool-for-user/{id}', 'ToolController@observeToolForUser')
            ->name('observe-tool-for-user');
    });

    Route::post('message', 'MessagesController@sendMessage')->name('send-message');

    Route::resource('building-notes', 'BuildingNoteController')->only('store');

    Route::group(['prefix' => 'building-status', 'as' => 'building-status.'], function () {
        Route::post('set-status', 'BuildingStatusController@setStatus')->name('set-status');
        Route::post('set-appointment-date',
            'BuildingStatusController@setAppointmentDate')->name('set-appointment-date');
    });
});

Route::group(['middleware' => ['current-role:cooperation-admin|coach|coordinator|super-admin']], function () {
    Route::group(['as' => 'buildings.', 'prefix' => 'buildings'], function () {
        Route::get('show/{buildingId}', 'BuildingController@show')->name('show');

        Route::group(['middleware' => ['current-role:cooperation-admin|coordinator|super-admin']], function () {
            Route::get('{building}/edit', 'BuildingController@edit')->name('edit');
            Route::put('{building}', 'BuildingController@update')->name('update');
        });
    });
});

/* Section for the cooperation-admin and coordinator */
Route::group(['prefix' => 'cooperatie', 'as' => 'cooperation.', 'namespace' => 'Cooperation', 'middleware' => ['current-role:cooperation-admin|coordinator']], function () {
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('', 'UserController@index')->name('index');
        Route::get('create', 'UserController@create')->name('create');
        Route::post('create', 'UserController@store')->name('store');

        Route::group(['middleware' => 'current-role:cooperation-admin'], function () {
            Route::delete('delete', 'UserController@destroy')->name('destroy');
        });
    });

    Route::resource('coaches', 'CoachController')->only(['index', 'show']);

    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('', 'ReportController@index')->name('index');
        Route::get('generate/{fileType}', 'ReportController@generate')->name('generate');
    });

    Route::resource('questionnaires', 'QuestionnaireController')
        ->middleware('current-role:cooperation-admin');
    // not in the cooperation-admin group, probably need to be used for hte coordinator aswell.
    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire', 'middleware' => ['current-role:cooperation-admin']], function () {
        Route::delete('delete-question/{questionId}', 'QuestionnaireController@deleteQuestion')->name('delete');
        Route::delete('delete-option/{questionId}/{optionId}', 'QuestionnaireController@deleteQuestionOption')->name('delete-question-option');
        Route::post('set-active', 'QuestionnaireController@setActive')->name('set-active');
    });

    /* Section for the coordinator */
    Route::group(['prefix' => 'coordinator', 'as' => 'coordinator.', 'namespace' => 'Coordinator', 'middleware' => ['current-role:coordinator']], function () {
        // needs to be the last route due to the param
        Route::get('home', 'CoordinatorController@index')->name('index');
    });

    /* section for the cooperation-admin */
    Route::group(['prefix' => 'cooperation-admin', 'as' => 'cooperation-admin.', 'namespace' => 'CooperationAdmin', 'middleware' => ['current-role:cooperation-admin|super-admin']], function () {
        Route::group(['prefix' => 'steps', 'as' => 'steps.'], function () {
            Route::get('', 'StepController@index')->name('index');
            Route::post('set-active', 'StepController@setActive')->name('set-active');
        });

        // needs to be the last route due to the param
        Route::get('home', 'CooperationAdminController@index')->name('index');
    });
});

/* Section for the super admin */
Route::group(['prefix' => 'super-admin', 'as' => 'super-admin.', 'namespace' => 'SuperAdmin', 'middleware' => ['current-role:super-admin']], function () {
    Route::get('home', 'SuperAdminController@index')->name('index');

    Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
        Route::get('', 'UserController@index')->name('index');
        Route::get('search', 'UserController@filter')->name('filter');
    });

    Route::resource('questionnaires', 'QuestionnaireController')->parameter('questionnaires', 'questionnaire');
    Route::post('questionnaires/copy', 'QuestionnaireController@copy')->name('questionnaire.copy');
//                    Route::group(['as' => 'questionnaires.', 'prefix' => 'questionnaire'], function () {
//                        Route::get('', 'QuestionnaireController@index')->name('index');
//                        Route::get('show', 'QuestionnaireController@show')->name('show');
//                    });

    Route::resource('key-figures', 'KeyFiguresController')->only('index');
    Route::resource('translations', 'TranslationController')->except(['show'])->parameters(['id' => 'group']);

    /* Section for the cooperations */
    Route::group(['prefix' => 'cooperations', 'as' => 'cooperations.', 'namespace' => 'Cooperation'], function () {
        Route::get('', 'CooperationController@index')->name('index');
        Route::delete('destroy/{cooperationToDestroy}', 'CooperationController@destroy')->name('destroy');
        Route::get('edit/{cooperationToEdit}', 'CooperationController@edit')->name('edit');
        Route::get('create', 'CooperationController@create')->name('create');
        Route::post('', 'CooperationController@store')->name('store');
        Route::post('edit', 'CooperationController@update')->name('update');

        /* Actions that will be done per cooperation */
        Route::group(['prefix' => '{cooperationToManage}/', 'as' => 'cooperation-to-manage.'],
            function () {
                Route::resource('home', 'HomeController')->only('index');

                Route::resource('cooperation-admin', 'CooperationAdminController')->only(['index']);
                Route::resource('coordinator', 'CoordinatorController')->only(['index']);
                Route::resource('users', 'UserController')->only(['index', 'show']);
                Route::post('users/{id}/confirm', 'UserController@confirm')->name('users.confirm');
            });
    });
});

/* Section for the coach */
Route::group(['prefix' => 'coach', 'as' => 'coach.', 'namespace' => 'Coach', 'middleware' => ['current-role:coach']], function () {
    Route::group(['prefix' => 'buildings', 'as' => 'buildings.'], function () {
        Route::get('', 'BuildingController@index')->name('index');
        Route::get('edit/{id}', 'BuildingController@edit')->name('edit');
        Route::post('edit', 'BuildingController@update')->name('update');
        Route::post('', 'BuildingController@setBuildingStatus')->name('set-building-status');

        Route::resource('details', 'BuildingDetailsController')->only('store');
    });

    // needs to be the last route due to the param
    Route::get('home', 'CoachController@index')->name('index');
});