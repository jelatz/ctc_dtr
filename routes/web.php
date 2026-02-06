<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DtrController;
use Inertia\Inertia;

Route::get('/', function(){
  return Inertia::render('Home');
})->name('home');
Route::match(['get', 'post'], '/get-schedules', [DtrController::class, 'getEmployeeAndSchedules'])->name('get-schedules');
Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');
