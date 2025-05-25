<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'auth.user' => function () {
                return Auth::user();
            },
        ]);

        RateLimiter::for('api', function (Request $request) {
            return app()->environment('testing')
                ? Limit::none() // Desativa rate limit nos testes
                : Limit::perMinute(60); // Limite padrão em outros ambientes
        });

        Vite::prefetch(concurrency: 3);
    }
}
