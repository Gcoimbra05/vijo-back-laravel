<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Illuminate\Queue\Events\JobProcessing::class => [
            \App\Listeners\CheckUserCreditBalance::class,
        ],
        \Illuminate\Queue\Events\JobProcessed::class => [
            \App\Listeners\DeductJobUserCredits::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}