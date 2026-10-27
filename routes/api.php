<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductControllerApi;
use App\Http\Controllers\Api\CategoryControllerApi;
use App\Http\Controllers\Api\AddressControllerApi;
use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\Api\ShippingControllerApi;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\Api\BannerControllerApi;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PushNotificationController;

use App\Http\Controllers\Api\CarDummyController;
use App\Http\Controllers\Api\PaymentDummyController;
use App\Http\Controllers\FcmController;

Route::post(
    '/apicn/fcm/register-token',
    [FcmController::class, 'registerToken']
);
Route::post(
    '/apicn/midtrans/callback',
    [PaymentDummyController::class, 'callback2']
)->name('midtransDummy.callback');
Route::get(
    '/apicn/orders/{idMCustomer}/{orderId}/sync-payment',
    [PaymentDummyController::class, 'syncPayment']
);
Route::get(
    '/apicn/orders/{idMCustomer}',
    [PaymentDummyController::class, 'history']
);

Route::get(
    '/apicn/orders/{idMCustomer}/{orderId}',
    [PaymentDummyController::class, 'historyDetail']
);
Route::post('/apicn/payment', [PaymentDummyController::class, 'store']);
Route::prefix('cart')->group(function () {
Route::post('/cart', [CarDummyController::class, 'store']);
Route::get('/show', [CarDummyController::class, 'index']);
});










Route::middleware('auth:sanctum')->post(
    '/admin/notifications/send',
    [PushNotificationController::class, 'send']
);
Route::post('/midtrans/callback', [
    MidtransCallbackController::class,
    'handle',
])->name('midtrans.callback');
Route::middleware('auth:sanctum')
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        // GET
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::get('/unread', [NotificationController::class, 'unread'])
            ->name('unread');

        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
            ->name('unread-count');

        // PATCH
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('read-all');

        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('read');

        // DELETE
        Route::delete('/{id}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });





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
Route::get('/categories/{id}', [CategoryControllerApi::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressControllerApi::class, 'index']);
    Route::post('/addresses', [AddressControllerApi::class, 'store']);
    Route::put('/addresses/{id}', [AddressControllerApi::class, 'update']);
    Route::delete('/addresses/{id}', [AddressControllerApi::class, 'destroy']);
});

//order
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderControllerApi::class, 'store']);

    Route::get('/orders/history', [OrderControllerApi::class, 'history']);

    // Detail pesanan
    Route::get('/orders/{id}', [OrderControllerApi::class, 'show']);

    // Status pembayaran
    Route::get('/orders/status/{id}', [
        OrderControllerApi::class,
        'checkPaymentStatus',
    ]);
});

//Raja ongkir
Route::post('/shipping/cost', [ShippingControllerApi::class, 'cost']);

//banner
Route::get('/banners', [BannerControllerApi::class, 'index']);
Route::get('/banners/{id}', [BannerControllerApi::class, 'show']);