<?php

use App\Http\Controllers\TraderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LongPollingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// route for login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);


Route::middleware('auth.multiGuard:student,teacher,parent,admin')->group(function () {
    Route::get('/state', [TraderController::class, 'getState']);

    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/getAccountOrders', [OrderController::class, 'getAccountOrders']);
    Route::post('/longPolling', [LongPollingController::class, 'getDataLongPolling']);

});
