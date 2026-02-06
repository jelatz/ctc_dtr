<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DtrController;

// Home page - GET only
Route::get('/', function () {
    return inertia('Home');
})->name('home');

// Get schedules - POST only (since you're using formData.post())
Route::post('/get-schedules', [DtrController::class, 'getEmployeeAndSchedules'])->name('get-schedules');

// Confirm DTR - POST only
Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');