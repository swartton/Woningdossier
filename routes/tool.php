<?php

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here are all tool related routes.
| Namespace: Tool
| Prefix: tool
| As: tool.
*/

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