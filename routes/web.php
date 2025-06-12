<?php

use App\Http\Controllers\ForgetPasswordManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use League\Csv\Query\Row;

Route::get('/', function () {
    return view('welcome');
}) -> name('home');

Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

Route::get('/registration', [AuthController::class, 'registration'])->name('registration');

Route::post('/registration', [AuthController::class, 'registrationPost'])->name('registration.post');

Route::get('/logout', [AuthController::class, 'logout']) -> name('logout');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/profile', function () {
        return "Hi";
    });
});

Route::get('/forget-password', [ForgetPasswordManager::class, "forgetPassword"]) -> name('forget.password');

Route::post('/forget-password', [ForgetPasswordManager::class, "forgetPasswordPost"]) -> name('forget.password.post');

Route::get('/reset-password/{token}', [ForgetPasswordManager::class, 'resetPassword']) -> name('reset.password');

Route::post('/reset-password', [ForgetPasswordManager::class, 'resetPasswordPost']) -> name('reset.password.post');
