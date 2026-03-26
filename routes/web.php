<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DtrController;


Route::inertia('/', 'Home')->name('home');
Route::post('/get-schedules', [DtrController::class, 'getEmployeeAndSchedules'])->name('get-schedules');
Route::get('/get-schedules', function () {
    return redirect()->route('home');
});
Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');
