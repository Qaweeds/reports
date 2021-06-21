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
        'App\Console\Commands\CopyXls',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('inspire')->hourly();
        $schedule->call('App\Http\Controllers\DistributorController@updateSuperStores')->dailyAt('05:42');
        $schedule->call('App\Http\Controllers\DistributorController@updateDistributors')->dailyAt('05:40');
        $schedule->call('App\Http\Controllers\DistributorController@updateDistributorsstaff')->dailyAt('05:36');
        $schedule->call('App\Http\Controllers\DistributorController@updateDistributorsTimeSheet')->dailyAt('05:35');
        $schedule->call('App\Http\Controllers\XlsCopyController@saveCopy')->dailyAt('05:33');
//        $schedule->command('command:CopyXls')->dailyAt('17:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
