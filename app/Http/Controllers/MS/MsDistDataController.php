<?php

namespace App\Http\Controllers\MS;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MsDistDataController extends Controller
{
    public static function getData()
    {
        dd(__METHOD__);
        $end = Carbon::now();
        $start = Carbon::now()->subDays(28);
        $sql = "SELECT a.[Date],
                        stores.Name as Container,
                        employees.Name,
                        a.[SaleSum]
                FROM [DWH].[Fact].[SalesRR_rr] as a
                inner join [DWH].[Dim].[StoresRR] as stores on a.[ContainerIDx] = stores.id
                inner join [DWH].[Dim].[EmployeesRR] as employees on a.[PersonIDx] = employees.id
                WHERE a.[Date] BETWEEN '".$start."' and '".$end."'
                ";
        $q = (Carbon::now()->getPreciseTimestamp(3));
        $arr = MsConnectController::msQuery($sql);
        echo (Carbon::now()->getPreciseTimestamp(3) - $q), '<br>';
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i]['Date'] = Carbon::parse($arr[$i]['Date'])->format('Y-m-d');
            $dates[] = $arr[$i]['Date'];
            $names[] = $arr[$i]['Name'];
        }
        $dates = array_unique($dates);
        rsort($dates);
        foreach ($arr as $data) {
            $bigData[$data['Name']][$data['Container']][$data['Date']] = (int)$data['SaleSum'];


        }

        foreach ($dates as $date) {
            foreach ($bigData as & $data_q){
                foreach ($data_q as & $data_qq){
                    if(!array_key_exists($date, $data_qq)){
                        $data_qq = array_merge($data_qq, array($date => null));
                    }
                }
            }
        }


        ksort($bigData);
        foreach ($bigData as & $emp) {
            foreach ($emp as & $value) {
                krsort($value);
            }
        }

        return array($bigData, $dates);
    }
}
