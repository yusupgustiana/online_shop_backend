<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductControllerApi;
use App\Http\Controllers\Api\CategoryControllerApi;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\Api\ShippingControllerApi;
use App\Http\Controllers\MidtransCallbackController;

Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle']);


Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//login
Route::post('/login', [AuthController::class, 'login']);
//product routes

Route::get('/products', [ProductControllerApi::class, 'index']);
Route::get('/products/{id}', [ProductControllerApi::class, 'show']);

//category routes

Route::get('/categories', [CategoryControllerApi::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
});

//order
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderControllerApi::class, 'store']);
    Route::get('/orders/history', [OrderControllerApi::class, 'history']);
    Route::get('/orders/{id}', [OrderControllerApi::class, 'show']);
    
});

//Raja ongkir
Route::post('/shipping/cost', [ShippingControllerApi::class, 'cost']);

