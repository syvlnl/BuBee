<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\TargetController;
use App\Http\Controllers\Api\v1\TransactionController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    
    Route::middleware(['auth:sanctum', 'token.expiry'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('users.targets', TargetController::class);
        Route::apiResource('users.transactions', TransactionController::class);
        Route::get('users/{user}/income', [TransactionController::class, 'getIncome'])
            ->name('users.income');
        Route::get('users/{user}/expense', [TransactionController::class, 'getExpense'])
            ->name('users.expense');
        Route::apiResource('users.categories', CategoryController::class);
    });
});