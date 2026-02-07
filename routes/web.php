<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DtrController;

Route::inertia('/', 'Home')->name('home');

Route::post('/get-schedules', [
    DtrController::class,
    'getEmployeeAndSchedules',
])->middleware(\App\Http\Middleware\DebugRequests::class)->name('get-schedules');

Route::post('/confirm-dtr', [
    DtrController::class,
    'addDtr',
])->middleware(\App\Http\Middleware\DebugRequests::class)->name('confirm-dtr');
