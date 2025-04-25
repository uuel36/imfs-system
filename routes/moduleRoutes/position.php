<?php

use Illuminate\Support\Facades\Route;

Route::prefix('position')->group(function() {
    Route::get('/get-positions', [App\Http\Controllers\Finance\PositionController::class, 'getPositions']);
    Route::post('/store-position', [App\Http\Controllers\Finance\PositionController::class, 'storePosition']);
    Route::post('/update-position/{id}', [App\Http\Controllers\Finance\PositionController::class, 'updatePosition']);
    Route::post('/delete-position/{id}', [App\Http\Controllers\Finance\PositionController::class, 'deletePosition']);
    Route::get('/get-position-list', [App\Http\Controllers\Finance\PositionController::class, 'getPositionList']);
    Route::get('/get-role-list', [App\Http\Controllers\Finance\PositionController::class, 'getRoleList']);
});
