<?php
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\TargetController;
use App\Http\Controllers\Api\v1\TransactionController;
use App\Http\Controllers\Api\v1\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('users.targets', TargetController::class);
    Route::apiResource('users.transactions', TransactionController::class);
    Route::apiResource('users.categories', CategoryController::class);
});