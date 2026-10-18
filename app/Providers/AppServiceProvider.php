<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') === 'production') {
            $url->forceScheme('https');
        }

        RateLimiter::for('analyze', function (Request $request) {
            return Limit::perMinute(config('business.rate_limit_analyze', 10))
                ->by($request->ip());
        });

        RateLimiter::for('re-analyze', function (Request $request) {
            return Limit::perMinute(config('business.rate_limit_re_analyze', 10))
                ->by($request->ip());
        });
    }
}
