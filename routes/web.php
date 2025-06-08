<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DtrController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::post('/check-employee', [DtrController::class, 'checkEmployee'])
    ->name('check-employee');

Route::post('/confirm-dtr', [DtrController::class, 'addDtr'])->name('confirm-dtr');
