<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Register your custom commands here
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('credits:topup-free-users')
             ->daily()
             ->at('00:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}