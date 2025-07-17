<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->group(function () {
    // Properties
    Route::apiResource('properties', PropertyController::class);
    Route::post('properties/{property}/check-availability', [PropertyController::class, 'checkAvailability']);
    
    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::post('bookings/calculate-price', [BookingController::class, 'calculatePrice']);
    
    // Guests
    Route::apiResource('guests', GuestController::class);
    
    // Extras
    Route::apiResource('extras', ExtraController::class);
});

// Authentication routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Test route
Route::get('/test', fn () => ['message' => 'API working']);