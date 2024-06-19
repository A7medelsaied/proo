<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PhoneVerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/logout', [ApiController::class, 'logout']);

    Route::post('/phone/verify', [PhoneVerificationController::class, 'sendVerificationCode']);
    Route::post('/phone/verify/code', [PhoneVerificationController::class, 'verifyCode']);
});
use App\Http\Controllers\Api\PasswordResetController;

Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [PasswordResetController::class, 'reset']);
