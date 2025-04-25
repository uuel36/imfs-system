<?php

use Illuminate\Support\Facades\Route;

Route::get('/deploy', [App\Http\Controllers\Warehouse\DeployController::class, 'index'])->name('deploy.index');

Route::group(['prefix' => 'deploy', 'middleware' => 'auth'], function () {
    Route::get('/get-deploy', [App\Http\Controllers\Warehouse\DeployController::class, 'getDeploy']);
    Route::get('/get-deploy-not-returned', [App\Http\Controllers\Warehouse\DeployController::class, 'getDeployNotReturned']);
    Route::get('/get-deploy-not-returned-consumed', [App\Http\Controllers\Warehouse\DeployController::class, 'getDeployNotReturnedConsumed']);
   
    Route::get('/get-requesto', [App\Http\Controllers\Warehouse\DeployController::class, 'getRequesto']);
    Route::post('/store-deploy', [App\Http\Controllers\Warehouse\DeployController::class, 'storeDeploy']);
    Route::post('/update-deploy/{id}', [App\Http\Controllers\Warehouse\DeployController::class, 'updateDeploy']);
    Route::post('/delete-deploy/{id}', [App\Http\Controllers\Warehouse\DeployController::class, 'deleteDeploy']);
    Route::get('get-history', [App\Http\Controllers\StockHistoryController::class, 'getHistories']);
    Route::get('/get-return-tools', [App\Http\Controllers\Warehouse\DeployController::class, 'getReturnTools']);
    Route::post('/return-tool/{id}', [App\Http\Controllers\Warehouse\DeployController::class, 'returnTool']);
    // gettools
   

    Route::get('/get-tools', [App\Http\Controllers\Warehouse\DeployController::class, 'getTools']);
    Route::get('/get-request-order/{id}', [App\Http\Controllers\Warehouse\DeployController::class, 'getRequestOrder']);
   
});
