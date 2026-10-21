<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Memastikan URL asset menggunakan HTTPS di production Railway
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            $cartCount = 0;

            if (auth()->check()) {
                $cartCount = Cart::where(
                    'user_id',
                    auth()->id()
                )->count();
            }

            $view->with('cartCount', $cartCount);
        });
    }
}