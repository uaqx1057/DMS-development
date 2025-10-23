<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Employee\{CreateEmployee, EditEmployee, EmployeeList};
use App\Livewire\DMS\DriverTypes\{CreateDriverType, DriverTypeList, EditDriverType};
use App\Livewire\DMS\Drivers\{CreateDriver, DriverList, EditDriver, ShowDriver};
use App\Livewire\DMS\Businesses\{BusinessList, CreateBusiness, EditBusiness};
use App\Livewire\DMS\BusinessesId\{BusinessIdList, CreateBusinessId, EditBusinessId};
use App\Livewire\DMS\CoordinatorReport\{CoordinatorReportList, CreateCoordinatorReport, EditCoordinatorReport};
use App\Livewire\DMS\PlatformIdsReport\{PlatformIdsReportList};
use App\Livewire\DMS\Fields\{FieldList, CreateField};
use App\Livewire\DMS\Payroll\PayrollList;
use App\Livewire\DMS\RevenueReporting\RevenueReportingList;
use App\Livewire\Role\{CreateRole, EditRole, RoleList, RolePermission};
use Illuminate\Support\Facades\Route;
use App\Livewire\Vehicle\VehicleList;
use App\Livewire\Fuel\Index;
use App\Http\Controllers\FuelExportController;
use App\Livewire\Auth\Forgot;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Orders\OrderDetails;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Livewire\Auth\Reset;
use App\Livewire\Driver\Auth\Login as DriverLogin;
use App\Livewire\Driver\Dashboard as DriverDashboard;
use App\Livewire\Driver\Profile as DriverProfile;
use App\Livewire\Driver\Vehicle as DriverVehicle;
use App\Livewire\Driver\Order as DriverOrder;
use App\Livewire\Driver\FuelManagement;
use App\Http\Controllers\Auth\DriverLoginController;
use App\Livewire\Driver\DriverForgotPassword as ffg;



Route::post('/custom-login', [LoginController::class, 'customLogin'])->name('custom.login');
Route::get('/login', Login::class)->name('login');
// Authenticated Routes
Route::middleware('auth')->group(function () {
//Vehicle Routes
Route::get('/vehicle', VehicleList::class)->name('vehicle.index');
//route for fuel
Route::get('/fuel', Index::class)->name('fuel.index');
//export fuel request
Route::get('/fuel-export', [FuelExportController::class, 'export'])->name('fuel.export');
//export fuel expense
Route::get('/fuel-expense', [FuelExportController::class, 'exportExpenses'])->name('fuel.expense');
Route::get('/fuel-report-export', [Index::class, 'exportReport'])->name('fuel.report.export');
Route::get('/orders', OrderDetails::class)->name('orders.index');


    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // HR Routes
    Route::prefix('hr')->group(function () {

        // Role Routes
        Route::prefix('role')->as('role.')->group(function () {
            Route::get('/', RoleList::class)->name('index')->middleware('permission:' . config('const.ROLE') . ',' . config('const.VIEW'));
            Route::get('/create', CreateRole::class)->name('create')->middleware('permission:' . config('const.ROLE') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditRole::class)->name('edit')->middleware('permission:' . config('const.ROLE') . ',' . config('const.EDIT'));
            Route::get('/permission/{id}', RolePermission::class)->name('permission')->middleware('permission:' . config('const.ROLE') . ',' . config('const.EDIT'));
        });

        // Employee Routes
        Route::prefix('employee')->as('employee.')->group(function () {
            Route::get('/', EmployeeList::class)->name('index')->middleware('permission:' . config('const.EMPLOYEES') . ',' . config('const.VIEW'));
            Route::get('/create', CreateEmployee::class)->name('create')->middleware('permission:' . config('const.EMPLOYEES') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditEmployee::class)->name('edit')->middleware('permission:' . config('const.EMPLOYEES') . ',' . config('const.EDIT'));
        });

    });

    // DMS Routes
    Route::prefix('dms')->group(function () {

        // Driver Types Routes
        Route::prefix('driver-types')->as('driver-types.')->group(function () {
            Route::get('/', DriverTypeList::class)->name('index')->middleware('permission:' . config('const.DRIVERTYPES') . ',' . config('const.VIEW'));
            Route::get('/create', CreateDriverType::class)->name('create')->middleware('permission:' . config('const.DRIVERTYPES') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditDriverType::class)->name('edit')->middleware('permission:' . config('const.DRIVERTYPES') . ',' . config('const.EDIT'));
        });

        // Drivers Routes
        Route::prefix('drivers')->as('drivers.')->group(function () {
            Route::get('/', DriverList::class)->name('index')->middleware('permission:' . config('const.DRIVERS') . ',' . config('const.VIEW'));
            Route::get('/create', CreateDriver::class)->name('create');
            // Route::get('/create', function(){
                // return 'Hello world';
            // })->name('create')
            // ->middleware('permission:' . config('const.DRIVERS') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditDriver::class)->name('edit')->middleware('permission:' . config('const.DRIVERS') . ',' . config('const.EDIT'));
            Route::get('/show/{id}', ShowDriver::class)->name('show')->middleware('permission:' . config('const.DRIVERS') . ',' . config('const.VIEW'));
        });

        // Business Fields Routes
        Route::prefix('business-field')->as('field.')->group(function () {
            Route::get('/', FieldList::class)->name('index')->middleware('permission:' . config('const.BUSINESSFIELDS') . ',' . config('const.VIEW'));
            Route::get('/create', CreateField::class)->name('create')->middleware('permission:' . config('const.BUSINESSFIELDS') . ',' . config('const.ADD'));
        });

        // Businesses Routes
        Route::prefix('businesses')->as('business.')->group(function () {
            Route::get('/', BusinessList::class)->name('index')->middleware('permission:' . config('const.BUSINESSES') . ',' . config('const.VIEW'));
            Route::get('/create', CreateBusiness::class)->name('create')->middleware('permission:' . config('const.BUSINESSES') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditBusiness::class)->name('edit')->middleware('permission:' . config('const.BUSINESSES') . ',' . config('const.EDIT'));
        });

        
        // Businesses Id Routes
        Route::prefix('businessesid')->as('businessid.')->group(function () {
            Route::get('/', BusinessIdList::class)->name('index')->middleware('permission:' . config('const.BUSINESSESID') . ',' . config('const.VIEW'));
            Route::get('/create', CreateBusinessId::class)->name('create')->middleware('permission:' . config('const.BUSINESSESID') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditBusinessId::class)->name('edit')->middleware('permission:' . config('const.BUSINESSESID') . ',' . config('const.EDIT'));
        });

        // Coordinator Reports Routes
        Route::prefix('coordinator-report')->as('coordinator-report.')->group(function () {
            Route::get('/', CoordinatorReportList::class)->name('index')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.VIEW'));
            Route::get('/create', CreateCoordinatorReport::class)->name('create')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditCoordinatorReport::class)->name('edit')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.EDIT'));
        });
        // Platform Ids Reports Routes
        Route::prefix('platform-ids-report')->as('platform-ids-report.')->group(function () {
            Route::get('/', PlatformIdsReportList::class)->name('index')->middleware('permission:' . config('const.PLATFORMIDSREPORT') . ',' . config('const.VIEW'));
         });

        // Payroll Routes
        Route::prefix('payroll')->as('payroll.')->group(function () {
            Route::get('/', PayrollList::class)->name('index')->middleware('permission:' . config('const.PAYROLL') . ',' . config('const.VIEW'));
        });

        // Revenue Reporting Routes
        Route::prefix('revenue-reporting')->as('revenue-reporting.')->group(function () {
            Route::get('/', RevenueReportingList::class)->name('index')->middleware('permission:' . config('const.REVENUEREPORTING') . ',' . config('const.VIEW'));
        });

    });

});




Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/', DriverLogin::class)->name('login');
    Route::get('/login', DriverLogin::class)->name('login');
    Route::post('/login', [DriverLoginController::class, 'login'])->name('login.submit');
    Route::get('/logout', [DriverLoginController::class, 'logout'])->name('driver.logout');

    
    Route::middleware('auth:driver')->group(function () {
    Route::get('/dashboard', DriverDashboard::class)
    ->name('dashboard');
    
     Route::get('/profile', DriverProfile::class)
    ->name('driver.profile');
    
     Route::get('/vehicle', DriverVehicle::class)
    ->name('driver.vehicle');
    
    Route::get('/order', DriverOrder::class)
    ->name('driver.order');
    
     Route::get('/fuel', FuelManagement::class)
    ->name('driver.fuel');
});

});


    
    Route::get('/driver/forgot-password',[ffg::class])->name('driver.password.request');
  //  Route::get('driver/reset-password/{token}', \App\Livewire\DriverResetPassword::class)->name('driver.password.reset');






//Admin Forgot Password
Route::get('/forgot-password', Forgot::class)->name('admin.password.request');
Route::post('forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
Route::get('/reset-password', Reset::class)->name('admin.password.reset');
Route::post('reset-password', [Reset::class, 'reset'])->name('admin.password.update');



use Illuminate\Support\Facades\Storage;

Route::get('/preview-file/{file}', function ($file) {
    // Adjust path and disk as per your file storage
    $path = storage_path("app/livewire-tmp/{$file}");

    if (!file_exists($path)) {
        abort(404, 'File not found');
    }

    return response()->file($path);
})->name('preview-file');





























