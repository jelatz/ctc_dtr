<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DtrController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return Inertia::render('Home');
})->name('homepage');

Route::post('/get-schedules', [DtrController::class, 'getSchedules'])
    ->name('get-schedules');

Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');
