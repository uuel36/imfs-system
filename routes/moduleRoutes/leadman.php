<?php

use Illuminate\Support\Facades\Route;

Route::get('/manage-staff', [App\Http\Controllers\Leadman\TeamController::class, 'manage_staff'])->name('staff.manage');

