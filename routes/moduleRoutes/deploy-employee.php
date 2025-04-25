<?php

use Illuminate\Support\Facades\Route;

Route::get('/deploy-employee', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'index'])->name('deploy-employee.index');

Route::group(['prefix' => 'deploy-employee', 'middleware' => 'auth'], function () {

    Route::get('/get-deploy', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'getDeploy']);
    Route::get('/search-deploy-by-date', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'searchDeployByDate']);
    Route::post('/store-deploy', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'storeDeploy']);
    Route::post('/update-deploy/{id}', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'updateDeploy']);
    Route::post('/delete-deploy/{id}', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'deleteDeploy']);
    Route::get('/get-deploy-by-area', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'getDeployByArea']);
    Route::get('/get-deploy-by-team-id-and-date/', [App\Http\Controllers\Leadman\DeployEmployeeController::class, 'getDeployByTeamIdAndDate']);
});
