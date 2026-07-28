<?php

namespace App\Providers;

use App\Models\Screening;
use App\Policies\ScreeningPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Screening::class, ScreeningPolicy::class);

        RateLimiter::for('screening-start', function (Request $request): Limit {
            return Limit::perMinute(10)->by(
                ($request->user()?->id ?? 'guest').'|'.$request->ip()
            );
        });

        RateLimiter::for('screening-submit', function (Request $request): Limit {
            $screeningId = $request->route('screening')?->id ?? 'unknown';

            return Limit::perMinute(10)->by(
                ($request->user()?->id ?? 'guest').'|'.$screeningId.'|'.$request->ip()
            );
        });
    }
}
