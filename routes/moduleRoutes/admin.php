<?php
use Illuminate\Support\Facades\Route;

Route::get('/company', [App\Http\Controllers\Company\CompanyController::class, 'index'])->name('company.index');
Route::get('/company/create-job', [App\Http\Controllers\Company\CompanyController::class, 'createJob'])->name('company.create_job');
Route::get('/company/jobs', [App\Http\Controllers\Company\CompanyController::class, 'jobs'])->name('company.jobs');
Route::get('/company/applicants', [App\Http\Controllers\Company\CompanyController::class, 'applicants'])->name('company.applicants');
Route::get('/company/profile', [App\Http\Controllers\Company\CompanyController::class, 'profile'])->name('company.profile');
Route::get('admin/get-leadmans', [App\Http\Controllers\Company\CompanyController::class, 'getLeadmans'])->name('admin.get_leadmans');
Route::get('/admin/get-leadman-by-id/{id}', [App\Http\Controllers\Company\CompanyController::class, 'getLeadmanById'])->name('admin.get_leadman_by_id');
Route::post('admin/create-leadman/', [App\Http\Controllers\Company\CompanyController::class, 'createLeadman']);
// delete leadman
Route::delete('/admin/delete-leadman/{id}', [App\Http\Controllers\Company\CompanyController::class, 'deleteLeadman']);
Route::get('/admin/get-current-user-role/', [App\Http\Controllers\Company\CompanyController::class, 'getCurrentUserRole']);
Route::post('/company/approve-request/{id}', [App\Http\Controllers\Company\CompanyController::class, 'approveRequest'])->name('company.approve_request');
Route::post('/company/disapprove-request/{id}', [App\Http\Controllers\Company\CompanyController::class, 'disapproveRequest'])->name('company.disapprove_request');