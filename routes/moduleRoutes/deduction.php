<?php

use Illuminate\Support\Facades\Route;

Route::prefix('deduction')->group(function () {
    Route::get('/get-philhealth', [App\Http\Controllers\Finance\DeductionController::class, 'getPhilhealth']);
    Route::post('/store-philhealth', [App\Http\Controllers\Finance\DeductionController::class, 'storePhilhealth']);
    Route::post('/update-philhealth/{id}', [App\Http\Controllers\Finance\DeductionController::class, 'updatePhilhealth']);
    Route::post('/delete-philhealth/{id}', [App\Http\Controllers\Finance\DeductionController::class, 'deletePhilhealth']);

    Route::get('/get-sss', [App\Http\Controllers\Finance\DeductionController::class, 'getSSS']);
    Route::post('/store-sss', [App\Http\Controllers\Finance\DeductionController::class, 'storeSSS']);
    Route::post('/update-sss/{id}', [App\Http\Controllers\Finance\DeductionController::class, 'updateSSS']);
    Route::post('/delete-sss/{id}', [App\Http\Controllers\Finance\DeductionController::class, 'deleteSSS']);
});
