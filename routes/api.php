<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DtrController as ApiDtrController;

// Public route (no auth)
Route::get('/status', function () {
    return response()->json(['status' => 'API is working']);
});

// // Auth routes
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

// Protected routes (need token)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user(); // Current logged in user
//     });

    // Example resources
//     Route::apiResource('users', UserController::class);
//     Route::apiResource('posts', PostController::class);
// });
