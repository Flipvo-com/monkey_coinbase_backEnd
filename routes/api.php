<?php

use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentTransactionController;
use App\Http\Controllers\TestController;
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

Route::get('/test',  [TestController::class, 'test']);

// route for login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);


// todo - Youcef, what is this? I wish this didn't have student, teacher, parent, etc stuff
Route::middleware('auth.multiGuard:student,teacher,parent,admin')->group(function () {
    Route::get('/state', [TraderController::class, 'getState']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/getAccountOrders', [OrderController::class, 'getAccountOrders']);
    Route::post('/longPolling', [LongPollingController::class, 'getDataLongPolling']);
});


// Group routes for investment-related operations
Route::prefix('investments')->group(function () {
    // Fetch all investments
    Route::get('/', [InvestmentController::class, 'index']);

    // Create a new investment
    Route::post('/', [InvestmentController::class, 'store']);

    // Update an existing investment
    Route::put('/{id}', [InvestmentController::class, 'update']);

    // Delete an investment
    Route::delete('/{id}', [InvestmentController::class, 'destroy']);
});

// Group routes for investment transactions
Route::prefix('investment-transactions')->group(function () {
    // Fetch all transactions
    Route::get('/', [InvestmentTransactionController::class, 'index']);

    // Create a new transaction
    Route::post('/', [InvestmentTransactionController::class, 'store']);

    // Fetch transactions for a specific user
    Route::get('/user/{userId}', [InvestmentTransactionController::class, 'getByUser']);
});
