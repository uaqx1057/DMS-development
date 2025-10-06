<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Employee\{CreateEmployee, EditEmployee, EmployeeList};
use App\Livewire\DMS\DriverTypes\{CreateDriverType, DriverTypeList, EditDriverType};
use App\Livewire\DMS\Drivers\{CreateDriver, DriverList, EditDriver, ShowDriver};
use App\Livewire\DMS\Businesses\{BusinessList, CreateBusiness, EditBusiness};
use App\Livewire\DMS\CoordinatorReport\{CoordinatorReportList, CreateCoordinatorReport, EditCoordinatorReport};
use App\Livewire\DMS\Fields\{FieldList, CreateField};
use App\Livewire\DMS\Payroll\PayrollList;
use App\Livewire\DMS\RevenueReporting\RevenueReportingList;
use App\Livewire\Role\{CreateRole, EditRole, RoleList, RolePermission};
use Illuminate\Support\Facades\Route;
use App\Livewire\Vehicle\VehicleList;

Route::get('/login', Login::class)->name('login');

// Authenticated Routes
Route::middleware('auth')->group(function () {


//Vehicle Routes
Route::get('/vehicle', VehicleList::class)->name('vehicle.index');













    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');

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

        // Coordinator Reports Routes
        Route::prefix('coordinator-report')->as('coordinator-report.')->group(function () {
            Route::get('/', CoordinatorReportList::class)->name('index')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.VIEW'));
            Route::get('/create', CreateCoordinatorReport::class)->name('create')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.ADD'));
            Route::get('/edit/{id}', EditCoordinatorReport::class)->name('edit')->middleware('permission:' . config('const.COORDINATORREPORT') . ',' . config('const.EDIT'));
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
