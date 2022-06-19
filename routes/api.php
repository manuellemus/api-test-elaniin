<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ChangePasswordController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// route for products
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{id}', 'get_product');
    Route::post('/products/store', 'store');
    Route::put('/products/update/{id}', 'update');
    Route::delete('/products/{id}', 'destroy');
    Route::post('/products/search', 'search');
});

// route for users
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::get('/users/{id}', 'get_user');
    Route::post('/users/store', 'store');
    Route::put('/users/update/{id}', 'update');
    Route::delete('/users/{id}', 'destroy');
    Route::post('/users/search', 'search');
});

// route for auth
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('refresh', 'refresh');
    Route::post('logout', 'logout');
    Route::get('user', 'getAuthenticatedUser');
});

// route for reset and change password
Route::post('reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
Route::post('change-password', [ChangePasswordController::class, 'passwordResetProcess']);
