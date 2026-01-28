<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CreateDatabase;
use App\Commands\FetchAllArticles;
use App\Commands\FetchFullArticles;
use App\Commands\EmptyArticles;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        CreateDatabase::class,
        FetchAllArticles::class,
        FetchFullArticles::class,
        EmptyArticles::class,
         
     ];

    protected function schedule(Schedule $schedule)
    {

        $schedule->command('delete:oldpost')
        ->everySixHours();
        $schedule->command('cron:fetch-all-articles')
         ->everyThirtyMinutes();
        $schedule->command('digest:send')->dailyAt('08:00');
        // Send push notifications every 3 hours between 8 AM and 10 PM
        $schedule->command('notifications:send-push')->dailyAt('08:00');
        $schedule->command('notifications:send-push')->dailyAt('11:00');
        $schedule->command('notifications:send-push')->dailyAt('14:00');
        $schedule->command('notifications:send-push')->dailyAt('17:00');
        $schedule->command('notifications:send-push')->dailyAt('20:00');
        
        // Clean up old notifications daily at 2 AM
        $schedule->command('notifications:cleanup')->dailyAt('02:00');
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

