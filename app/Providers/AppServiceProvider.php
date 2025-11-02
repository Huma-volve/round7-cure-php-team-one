<?php

namespace App\Providers;

use App\Models\Booking;
use App\Observers\BookingObserver;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function () {
            $secret = (string) config('services.stripe.secret', env('STRIPE_SECRET'));
            return new StripeClient($secret);
        });

        // اربط عميل PayPal بدون تهيئة مبكرة. التهيئة ستتم داخل الـ Gateway عند الحاجة فقط.
        $this->app->singleton(PayPalClient::class, function () {
            return new PayPalClient();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Booking::observe(BookingObserver::class);
    }
}
