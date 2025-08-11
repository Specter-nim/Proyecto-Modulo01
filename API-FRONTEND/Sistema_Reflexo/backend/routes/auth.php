<?php

use App\Http\Controllers\Auth\AuthenticatedController;
use App\Http\Controllers\Auth\GetRoleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::group([
    'middleware' => 'guest'
], static function () {
    Route::post('login', [AuthenticatedController::class, 'store']);
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('validate-email',[ResetPasswordController::class,'show']);
});

Route::group([
    'middleware'=> 'optional.auth'
],static function(){
    Route::post('verification/{user?}', [VerificationController::class, 'validateCode']);
    Route::post('sendVerifyCode/{user?}', [EmailController::class, 'sendVerifyCode']);
});

Route::group([
    'middleware' => 'auth:sanctum'
], static function () {
    Route::get('get-role',[GetRoleController::class,'show']);
    Route::delete('logout', [AuthenticatedController::class, 'destroy']);
    Route::put('change-password',[ChangePasswordController::class,'update']);

    // Nueva ruta para validar la contraseña actual
    Route::post('validate-password', [ChangePasswordController::class, 'validatePassword']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile',[ProfileController::class, 'update']);
});