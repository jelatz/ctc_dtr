<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DtrController;


Route::inertia('/', 'Home')->name('home');
Route::match(['get', 'post'], '/get-schedules', [DtrController::class, 'getEmployeeAndSchedules'])->name('get-schedules');
Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');
