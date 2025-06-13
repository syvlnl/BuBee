<?php
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\TargetController;
use App\Http\Controllers\Api\v1\TransactionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('targets', TargetController::class);
    Route::apiResource('transactions', TransactionController::class);
});