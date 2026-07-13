<?php

use Illuminate\Support\Facades\Route;


// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\BannerAdminController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\User\HomeUserController;
use App\Http\Controllers\User\CartUserController;
use App\Http\Controllers\User\ProductUserController;
use App\Http\Controllers\User\CheckoutControllerUser;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\Api\OrderControllerApi;
use App\Http\Controllers\AddressController;



/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/


Route::middleware('guest')->group(function () {


    Route::get('/', 
        [AuthController::class, 'showLogin']
    )->name('login');


    Route::post('/login',
        [AuthController::class, 'login']
    )->name('login.process');


    Route::get('/register',
        [AuthController::class, 'showRegister']
    )->name('register');


    Route::post('/register',
        [AuthController::class, 'register']
    )->name('register.process');

});



/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/


Route::post('/logout',
    [AuthController::class, 'logout']
)
->middleware('auth')
->name('logout');





/*
|--------------------------------------------------------------------------
| REDIRECT HOME
|--------------------------------------------------------------------------
*/


Route::get('/home',
    [HomeController::class,'index']
)
->middleware('auth')
->name('home');






/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/


Route::prefix('admin')
->middleware([
    'auth',
    'role:ADMIN'
])
->name('admin.')
->group(function(){


    Route::get('/dashboard', function(){

        return view('admin.dashboard');

    })
    ->name('dashboard');


    Route::resource(
        'user',
        UserAdminController::class
    );


    Route::resource(
        'category',
        CategoryAdminController::class
    );


    Route::resource(
        'product',
        ProductController::class
    );


    Route::resource(
        'banners',
        BannerAdminController::class
    );


});








/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/


Route::prefix('user')
->middleware([
    'auth',
    'role:USER'
])
->name('user.')
->group(function(){



    // Dashboard
    Route::get('/dashboard',
        [HomeUserController::class,'index']
    )
    ->name('dashboard');




    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */


    Route::get('/product/{id}',
        [ProductUserController::class,'show']
    )
    ->name('product.show');






    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */


    Route::post('/cart/add',
        [CartUserController::class,'add']
    )
    ->name('cart.add');


    Route::get('/cart',
        [CartUserController::class,'index']
    )
    ->name('cart.index');


    Route::post('/cart/update/{id}',
        [CartUserController::class,'update']
    )
    ->name('cart.update');


    Route::delete('/cart/remove/{id}',
        [CartUserController::class,'remove']
    )
    ->name('cart.remove');






    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */


    Route::get('/checkout',
        [CheckoutControllerUser::class,'index']
    )
    ->name('checkout.index');


    Route::post('/checkout/process',
        [CheckoutControllerUser::class,'process']
    )
    ->name('checkout.process');







    /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */


    Route::get('/orders',
        [OrderController::class,'index']
    )
    ->name('order.index');






    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */


    Route::get('/payment/{id}',
        [PaymentController::class,'show']
    )
    ->name('payment.show');



});






/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK
|--------------------------------------------------------------------------
*/


Route::post('/midtrans/callback',
    [OrderControllerApi::class,'callback']
);







/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/


Route::middleware('auth')
->group(function(){


    Route::get('/profile',
        [ProfileController::class,'show']
    )
    ->name('profile.show');


    Route::post('/profile',
        [ProfileController::class,'update']
    )
    ->name('profile.update');

});








/*
|--------------------------------------------------------------------------
| ADDRESS
|--------------------------------------------------------------------------
*/


Route::middleware('auth')
->group(function(){


    Route::resource(
        'address',
        AddressController::class
    );


    Route::post('/address/{id}/default',
        [AddressController::class,'setDefault']
    )
    ->name('address.default');


});







/*
|--------------------------------------------------------------------------
| RAJA ONGKIR
|--------------------------------------------------------------------------
*/


Route::get(
    '/rajaongkir/cities/{province}',
    [AddressController::class,'getCities']
);


Route::get(
    '/rajaongkir/districts/{city}',
    [AddressController::class,'getDistricts']
);


Route::post(
    '/checkout/ongkir',
    [AddressController::class,'getOngkir']
);






/*
|--------------------------------------------------------------------------
| DEBUG
|--------------------------------------------------------------------------
*/


Route::get('/clear-session',function(){


    auth()->logout();


    session()->invalidate();


    session()->regenerateToken();


    return "Session cleared";

});