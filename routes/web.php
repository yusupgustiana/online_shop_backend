<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Route::get('/', function () {
//     return view('pages.dashboard');
// });
Route::get('/', function () {
    return view('pages.auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard');
    })->name('home');
    Route::resource('user',userController::class);
    Route::resource('category',CategoryController::class);
    Route::resource('product',ProductController::class);
});


//register
// Route::get('register', function () {
//     return view('pages.auth.register');
// })->name('register');