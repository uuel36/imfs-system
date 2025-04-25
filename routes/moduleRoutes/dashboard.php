<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/total-employees', [App\Http\Controllers\DashboardController::class, 'getTotalEmployees']);
    Route::get('/total-areas', [App\Http\Controllers\DashboardController::class, 'getTotalAreas']);
    Route::get('/total-operations', [App\Http\Controllers\DashboardController::class, 'getTotalOperations']);
    Route::get('/total-present', [App\Http\Controllers\DashboardController::class, 'getPresentEmployee']);
    Route::get('/warehouse-data', [App\Http\Controllers\DashboardController::class, 'getWarehouseData']);
    Route::get('/overtime-employees', [App\Http\Controllers\DashboardController::class, 'getOvertimeEmployees']);
    // get total teams
    Route::get('/total-teams', [App\Http\Controllers\DashboardController::class, 'getTotalTeams']);
});
