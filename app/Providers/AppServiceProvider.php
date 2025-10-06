<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Emlo\EmloDatabaseLoader;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the StripeClient to the container with its secret key from config
        $this->app->singleton(StripeClient::class, function ($app) {
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        EmloDatabaseLoader::initialize();

    }
}
