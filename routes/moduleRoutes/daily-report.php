<?php

use Illuminate\Support\Facades\Route;

Route::get('/daily-report', [App\Http\Controllers\Leadman\DailyReportController::class, 'index'])->name('daily-report.index');
Route::prefix('daily-report')->as('daily-report.')->group(function () {

    Route::get('list', [App\Http\Controllers\Leadman\DailyReportController::class, 'getReports'])->name('list');
    Route::post('store', [App\Http\Controllers\Leadman\DailyReportController::class, 'storeReport'])->name('store');
    Route::post('update/{id}', [App\Http\Controllers\Leadman\DailyReportController::class, 'updateReport'])->name('update');
    Route::post('delete/{id}', [App\Http\Controllers\Leadman\DailyReportController::class, 'deleteReport'])->name('delete');
    Route::get('/team-report', [App\Http\Controllers\Leadman\DailyReportController::class, 'getTeamReport'])->name('team-report');
});

Route::get('/daily-report-team', [App\Http\Controllers\Leadman\DailyReportController::class, 'team'])->name('daily-report-team.index');
