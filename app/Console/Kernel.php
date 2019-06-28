<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\SendEodReport',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('eod:send')
            ->timezone('Asia/Kolkata')
            // ->dailyAt("23:40")
        ->everyMinute()
        // ->sendOutputTo($filePath)
        // ->emailOutputTo('monica.j@syslogyx.com')
            ->appendOutputTo('\inetpub\vhosts\vyako.com\apiprojectmgmttest.vyako.com\app\log.txt');

        $schedule->command('CronJob:cronjob')
            ->daily();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
