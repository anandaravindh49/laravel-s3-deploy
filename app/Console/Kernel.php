<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ProcessDeviceLogs;
use App\Jobs\SyncApprovedUsersToDevice;
use App\Jobs\AutoCheckoutExpiredVisitors;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process device logs every minute
        $schedule->job(new ProcessDeviceLogs())
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();

        // Sync approved users to devices every 5 minutes
        $schedule->job(new SyncApprovedUsersToDevice())
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Auto checkout expired visitors daily at midnight
        $schedule->job(new AutoCheckoutExpiredVisitors())
            ->dailyAt('00:00')
            ->withoutOverlapping();

        // Cleanup old audit logs monthly (retention: 6 months)
        $schedule->call(function () {
            \App\Models\AuditLog::where('created_at', '<', now()->subMonths(6))->delete();
        })->monthly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
