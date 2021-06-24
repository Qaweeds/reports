<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class Test extends Controller
{
    public function __invoke()
    {
//        dd(self::class);
        try {
            $names = DistributorsRetail::distinct('name')->pluck('name');
        } catch (\Throwable $e){
            dd($e);
        }



    }
}
