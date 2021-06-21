<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DistributorReportController extends Controller
{
    public static function getData($date = null)
    {
        $range = 28;
        $cityData = array();
        $distributor = new Distributor();
        $rangeEnd = (isset($date)) ? $date : Carbon::now()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays($range)->format('Y-m-d');
        $names = $distributor::distinct()->whereBetween('date', array($rangeStart, $rangeEnd))->orderBy('name')->pluck('name');
        $dates = $distributor::distinct()->whereBetween('date', array($rangeStart, $rangeEnd))->orderByDesc('date')->pluck('date')->toArray();
        foreach ($names as $name) {
            $data = DB::table('distributors')
                ->whereBetween('date', array($rangeStart, $rangeEnd))
                ->where('name', $name)
                ->orderByDesc('date')
                ->orderBy('store')
                ->get()->toArray();
            foreach ($data as $dayData) {
                $bigData[$name][$dayData->store][$dayData->date] = ($dayData->summ) ? $dayData->summ : null;  //Основные данные
            }
            $tabel = DB::table('distributor_timesheets')
                ->whereBetween('date', array($rangeStart, $rangeEnd))
                ->where('name', $name)
                ->get()->toArray();
            foreach ($tabel as $dayData) {
                $tableData[$name][$dayData->store][$dayData->date] = $dayData->work;   // ТАБЕЛЬ
            }
        }
        // Убираем "пустые магазины"
        foreach ($bigData as $name_empty_store => & $data_empty_store) {
            foreach ($data_empty_store as $key_empty_store => & $dates_empty_store) {
                $bgst[] = count($dates_empty_store);
                $q = 0;
                foreach ($dates_empty_store as & $d_empty_store) {
                    if ($d_empty_store != null) {
                        $q++;
                    }
                }
                if (!$q and count($data_empty_store) > 1) {
                    unset($data_empty_store[$key_empty_store]);
                }
            }
        }


        // Заполняем пустые дни. Костыль, если записи по реализатору не попадали в базу последние 45 дней (Отсутствует в выгрузке 1С)
        foreach ($dates as $date) {
            foreach ($bigData as & $data_q){
                foreach ($data_q as & $data_qq){
                    if(!array_key_exists($date, $data_qq)){
                        $data_qq = array_merge($data_qq, array($date => null));
                    }
                }
            }
        }
        //Сверяем с табелем
        foreach ($bigData as $name => $data) {
            foreach ($data as $store => $name_data) {
                foreach ($name_data as $date => $day_data) {
                    if (is_null($day_data) and isset($tableData[$name][$store][$date])) {
                        $check = $tableData[$name][$store][$date];
                        if ($check) $bigData[$name][$store][$date] = 0;
                    }
                }
            }
        }

        foreach ($bigData as & $emp) {
            foreach ($emp as & $value) {
                krsort($value);
            }
        }

        // Получаем сумму выручки
        foreach ($bigData as $name => $data) {
            if (count($data) > 1) {
                $arr[] = null;
                $arr[] = $name;
                foreach ($dates as $date) {
                    $check = null;
                    foreach ($tableData[$name] as $store => $day_data) {   //сверяем с табелем
                        if ($check) break;
                        if(isset($tableData[$name][$store][$date]))$check = $tableData[$name][$store][$date];
                    }
                    if ($check) {
                        $summ = $distributor::where('name', $name)->where('date', $date)->sum('summ');
                    } else {
                        $summ = null;
                    }
                    $arr[] = $summ;
                }
                $arr[] = null;
                $cityData[] = $arr;
                unset($arr);
            } else {
                foreach ($data as $store => $total) {
                    $arr[] = null;
                    $arr[] = $name;
                    foreach ($total as $summ) {
                        $arr[] = $summ;
                    }
                    $arr[] = null;
                    $cityData[] = $arr;
                    unset($arr);
                }
            }
            foreach ($data as $store => $total) {
                $arr[] = null;
                $arr[] = $store;
                foreach ($total as $summ) {
                    $arr[] = $summ;
                }
                $arr[] = null;
                $cityData[] = $arr;
                unset($arr);
            }
        }
        $header = array_merge(array(null, null, null, null), $dates);
        $header[] = null;
        array_unshift($cityData, $header);

        return $cityData;
    }


}