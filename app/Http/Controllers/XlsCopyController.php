<?php

namespace App\Http\Controllers;

use Adldap\Adldap;
use Adldap\Schemas\ActiveDirectory;
use App\Models\Distributor;
use App\Models\DistributorTimesheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class XlsCopyController extends Controller
{
    public function saveCopy()
    {
        $date = Carbon::now()->format('Y-m-d H:i');
        $success = \File::copy(base_path('storage/app/public/dist/Distributors.xlsx'), base_path('storage/app/public/distCopy/' . $date . '.xlsx'));
    }

    public function test()
    {
        $rangeEnd = Carbon::now()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays(45)->format('Y-m-d');
        $dates = DistributorTimeSheet::distinct()->whereBetween('date', array($rangeStart, $rangeEnd))->orderByDesc('date')->pluck('date')->toArray();
        foreach (array('30 Харьков, Inter-701 (175.175А, 175В)', '35 Харьков, Inter-701 (175D)', '81 Харьков, 206А, 206В', '91 Харьков, 21-02-291') as $store) {
            foreach ($dates as $date) {

                $names = DistributorTimeSheet::where('store',  $store )->where('work', 1)->where('date', $date)->pluck('name');
                if (count($names)) {
                    dd('q');
                    $summ = Distributor::where('store',  $store)->where('date', $date)->value('summ') / count($names);
                    foreach ($names as $name) {
                        Distributor::updateOrCreate(
                            [
                                'name' => $name,
                                'date' => $date,
                                'store' => $store
                            ],
                            [
                                'summ' => (int)$summ
                            ]
                        );

                    }
                }
            }
        }

    }

}

