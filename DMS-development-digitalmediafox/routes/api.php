<?php

use App\Http\Controllers\Api\V1\{AuthController, DriverProfileController, FuelRequestController, OrderController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function() {

    // Authentication Routes (login is outside of the middleware as it's public)
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login')->withoutMiddleware('auth:sanctum'); // Login route doesn't need authentication

        // Protected routes (Check-in, Check-out, Logout)
        Route::post('checkin', 'checkin');    // Check-in
        Route::post('checkout', 'checkout')->middleware('CheckLastCheckIn');
        Route::post('logout', 'logout')->withoutMiddleware('auth:sanctum');      // Logout
    });

    // Profile Routes
    Route::prefix('profile')->controller(DriverProfileController::class)->group(function () {
        Route::get('/', 'show');              // Get driver profile
        Route::get('/businesses', 'getBusinesses'); // Get associated businesses
        Route::get('/stats', 'getStats');     // Get driver stats
        Route::get('/get-unique-order-business', 'getUniqueOrderBusiness')->middleware('CheckLastCheckIn');     // Get driver stats
    });

    // Order Routes
    Route::prefix('order')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index');      // List all orders
        Route::post('/', 'create')->middleware('CheckLastCheckIn');    // Create a new order
        Route::put('/{order}', 'update')->middleware('CheckLastCheckIn'); // Update an existing order
    });

    // Fuel Routes
    Route::prefix('fuel')->controller(FuelRequestController::class)->group(function () {
        Route::get('/', 'index');      // List all orders
        Route::post('/', 'create');    // Create a new order
    });

});
