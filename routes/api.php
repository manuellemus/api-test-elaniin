<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('products','ProductController@index');
// Route::get('products/{id}','ProductController@get_product');
// Route::post('products/store','ProductController@store');
// Route::put('products/update/{id}','ProductController@update');
// Route::delete('products','ProductController@destroy');

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{id}', 'get_product');
    Route::post('/products/store', 'store');
    Route::put('/products/update/{id}', 'update');
    Route::delete('/products/{id}', 'destroy');
    Route::post('/products/search', 'search');

});
