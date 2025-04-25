<?php

use Illuminate\Support\Facades\Route;

Route::get('/linear-regression', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'index'])->name('linear.index');

Route::prefix( 'linear')
    ->middleware('auth')
    ->group(function() {
        Route::get('/get-linears', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'getLinears']);
        Route::post('/store-linear', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'storeLinear']);
        Route::post('/update-linear/{id}', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'updateLinear']);
        Route::post('/delete-linear/{id}', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'deleteLinear']);
        Route::get('/get-data/{id}', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'getData']);
        Route::get('/get-data-graph', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'getDataGraph']);
        Route::get('/get-data-graph-by-year', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getDataGraphByYear']);
        Route::get('/get-forecast', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getForecast']);
        Route::get('/get-data-graph-by-year-x1', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getDataGraphByYearX1']);
        Route::get('/get-data-graph-by-year-x2', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getDataGraphByYearX2']);
        Route::get('/get-data-graph-by-year-y', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getDataGraphByYearY']);
        Route::get('/get-data-graph-by-year-all', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getDataGraphByYearAll']);
        Route::get('/get-stem-data-graph-by-year', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getStemDataGraphByYear']);
        Route::get('/get-stem-data-graph-by-yearp', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getStemDataGraphByYearp']);
        Route::get('/get-stem-data-graph-by-year-all', [App\Http\Controllers\Leadman\LinearRegresionController::class,'getStemDataGraphByYearAll']);
        Route::get('/calculate-regression-line/{id}', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'calculateRegressionLine']);
        Route::get('/calculate-linear-values', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'calculateLinearValues']);
        Route::post('/predict-and-show', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'predictAndShow']);
    
        Route::get('/forecast-linear-values/{areaId}', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'forecastLinearValues']);
        Route::get('/get-all-linear-regressions', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'getAllLinearRegressions']);
        Route::post('/calculate-mse', [App\Http\Controllers\Leadman\LinearRegresionController::class, 'calculateMSE']);

    });
