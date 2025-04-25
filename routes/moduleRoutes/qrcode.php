<?php

use Illuminate\Support\Facades\Route;

Route::get('/qr-code', [App\Http\Controllers\HR\QrcodeController::class, 'index'])->name('qr-code.index');

Route::get('/generate-qr/{id}', [App\Http\Controllers\HR\QrcodeController::class, 'generateQR']);

Route::group(['prefix' => 'qrcode', 'middleware' => 'auth'], function () {
    Route::get('get-qrcodes', [App\Http\Controllers\HR\QrcodeController::class, 'getQrCode']);
    Route::get('/store-qrcode', [App\Http\Controllers\HR\QrcodeController::class, 'storeQrcode']);
    Route::get('/verified', [App\Http\Controllers\HR\QrcodeController::class, 'verified']);
    // get position by id
    Route::get('/get-position-by-id/{id}', [App\Http\Controllers\HR\QrcodeController::class, 'getPosition']);
});
