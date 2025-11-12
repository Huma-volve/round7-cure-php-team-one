<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Ticket;
use App\Models\Review;
use App\Policies\PaymentMethodPolicy;
use App\Observers\BookingObserver;
use App\Observers\ReviewObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        // Use Bootstrap 4 pagination view
        Paginator::defaultView('pagination::bootstrap-4');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-4');
        
        Booking::observe(BookingObserver::class);
        Review::observe(ReviewObserver::class);

        Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);

        // Share navbar data for admin layout - apply to master layout to ensure data is available on all pages
        View::composer(['admin.master', 'admin.layouts.navbar'], function ($view) {
            $user = Auth::user();
            $unreadCount = 0;
            $notifications = collect();
            $ticketsCount = 0;
            $avatarUrl = null;

            if ($user) {
                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $notifications = Notification::where('user_id', $user->id)
                    ->latest()
                    ->limit(5)
                    ->get();

                // Count open or pending tickets
                $ticketsCount = Ticket::whereIn('status', ['open', 'pending'])->count();

                $avatarUrl = $user->profile_image_path
                    ? asset('storage/' . $user->profile_image_path)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff';
            }

            $view->with(compact('unreadCount', 'notifications', 'ticketsCount', 'avatarUrl'));
        });
    }
}
