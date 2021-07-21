<?php

namespace App\Console\Commands;

use App\Jobs\DownloadDistributorsData;
use Illuminate\Console\Command;
use App\Models\User;

class Q extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo 'q';
    }
}
