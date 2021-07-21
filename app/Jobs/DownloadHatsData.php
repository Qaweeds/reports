<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadHatsData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cities = array('Харьков', 'Одесса', 'Хмельницкий', 'ИМ');

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    public function get($filename)
    {
        $xls = IOFactory::load(base_path('storage/app/public/dist/' . $filename . '.xlsx'));
        $xls = $xls->getActiveSheet()->toArray();
        return $xls;
    }


//    public function trim data
}
