<?php

namespace App\Console;

use App\BasicExtra;
use App\Console\Commands\SubscriptionChecker;
use App\Jobs\UpdateRequiredCpdPointJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update-req-cpd-point')->hourly();

        $schedule->command('subscription:check')->daily();

        // $schedule->command('password:expire')->dailyAt('00:01');
        $schedule->command('license:expire')->everyMinute();

        $schedule->command('queue:prune-batches --hours=12 --unfinished=24')->twiceDaily();

        $schedule->command('queue:work --timeout=120 --stop-when-empty')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
