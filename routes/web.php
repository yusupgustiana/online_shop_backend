<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeUserController;
use App\Http\Controllers\User\CartUserController;
use App\Http\Controllers\User\ProductUserController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\User\CategoryUserController;






/*
|--------------------------------------------------------------------------
| AUTH ROUTES (GUEST ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});


/*
|--------------------------------------------------------------------------
| LOGOUT (AUTH ONLY)
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| HOME (REDIRECT BY ROLE)
|--------------------------------------------------------------------------
*/

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:ADMIN'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('user', UserController::class);
        Route::resource('category', CategoryAdminController::class);
        Route::resource('product', ProductController::class);
    });


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('user')
    ->middleware(['auth', 'role:USER'])
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [HomeUserController::class, 'index'])
            ->name('dashboard');
        Route::post('/cart/add', [CartUserController::class, 'add'])->name('cart.add');
        Route::get('/cart', [CartUserController::class, 'index'])->name('cart.index');
        Route::post('/cart/update/{id}', [CartUserController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [CartUserController::class, 'remove'])->name('cart.remove');
        Route::get('/product/{id}', [ProductUserController::class, 'show'])->name('product.show');
 
    });


/*
|--------------------------------------------------------------------------
| PROFILE (SEMUA USER LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::post('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| CART ROUTES (USER ONLY)
|--------------------------------------------------------------------------
*/

Route::get('/cart', [CartUserController::class, 'index'])->name('cart.index');
Route::post('/cart/update/{id}', [CartUserController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartUserController::class, 'remove'])->name('cart.remove');
/*
|--------------------------------------------------------------------------
| DEBUG (OPSIONAL)
|--------------------------------------------------------------------------
*/

Route::get('/clear-session', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return 'Session cleared';
});