<?php

use App\Models\Leadman\LinearRegression;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    // check if user is logged in
    if(Auth::check()) {
        return view('home');
    }
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'auth'], function () {

    require_once base_path('routes/moduleRoutes/employee.php');
    require_once base_path('routes/moduleRoutes/warehouse.php');
    require_once base_path('routes/moduleRoutes/area.php');
    require_once base_path('routes/moduleRoutes/attendance.php');
    require_once base_path('routes/moduleRoutes/deploy.php');
    require_once base_path('routes/moduleRoutes/qrcode.php');
    require_once base_path('routes/moduleRoutes/deploy-employee.php');
    require_once base_path('routes/moduleRoutes/finance.php');
    require_once base_path('routes/moduleRoutes/harvest.php');
    require_once base_path('routes/moduleRoutes/team.php');
    require_once base_path('routes/moduleRoutes/task.php');
    require_once base_path('routes/moduleRoutes/month.php');
    require_once base_path('routes/moduleRoutes/dashboard.php');
    require_once base_path('routes/moduleRoutes/banana.php');
    require_once base_path('routes/moduleRoutes/linear.php');
    require_once base_path('routes/moduleRoutes/position.php');
    require_once base_path('routes/moduleRoutes/deduction.php');
    require_once base_path('routes/moduleRoutes/daily-operation.php');
    require_once base_path('routes/moduleRoutes/daily-report.php');
    require_once base_path('routes/moduleRoutes/leadman.php');
    require_once base_path('routes/moduleRoutes/admin.php');

    Route::get('/ge-payroll', [App\Http\Controllers\Finance\FinanceController::class, 'generatePayroll']);
    Route::post('/approve-request/{id}', [App\Http\Controllers\Company\CompanyController::class, 'approveRequest'])->name('approve-request');
     Route::get('/check-stock-quantity/{id}', [App\Http\Controllers\Company\CompanyController::class, 'checkStockQuantity'])->name('check-stock-quantity');
    Route::post('/disapprove-request/{id}', [App\Http\Controllers\Company\CompanyController::class, 'disapproveRequest'])->name('disapprove-request');
    Route::get('/scanner', function () {

        return view('scanner.index');

    });
    // Password Reset Routes

});

