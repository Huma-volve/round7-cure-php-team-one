<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Review;
use App\Observers\BookingObserver;
use App\Observers\ReviewObserver;
use Illuminate\Support\ServiceProvider;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function () {
            $secret = (string) config('services.stripe.secret', env('STRIPE_SECRET'));
            
            if (empty($secret) || $secret === 'sk_test_xxx' || $secret === 'YOUR_STRIPE_SECRET') {
                throw new \RuntimeException(
                    'Stripe API key is not configured. Please set STRIPE_SECRET in your .env file. ' .
                    'Get your API key from https://dashboard.stripe.com/apikeys'
                );
            }
            
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
        Review::observe(ReviewObserver::class);

    }
}
