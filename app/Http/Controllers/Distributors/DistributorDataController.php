<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Distributor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DistributorDataController extends BaseDistibutorsController
{


    public static function getData($date = null)
    {
        $distributor = parent::$distributor;
        $table = parent::$table;
        $range = 27;
//        $rangeEnd = (isset($date)) ? $date : Carbon::now()->format('Y-m-d');
//        $rangeStart = Carbon::parse($rangeEnd)->subDays($range)->format('Y-m-d');
        $rangeEnd = (isset($date)) ? $date : Carbon::now()->subDay()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays($range)->format('Y-m-d');
        $date_range = array($rangeStart, $rangeEnd);
        $group_cols = array('date', 'name', 'store');

        $dates = $distributor::distinct()->whereBetween('date',$date_range)->orderByDesc('date')->pluck('date')->toArray();
        $data = DB::table($table)->groupBy($group_cols)
            ->whereBetween('date', $date_range)
            ->orderBy('name')
            ->get(['name', 'date', 'store', 'summ']);
        $tabel = DB::table('distributor_timesheets')->groupBy($group_cols)
            ->whereBetween('date',$date_range)
            ->orderBy('name')
            ->get(['name', 'date', 'store', 'work']);

        foreach ($tabel as $dayData) {
            $tableData[$dayData->name][$dayData->store][$dayData->date] = $dayData->work;   // ТАБЕЛЬ
        }
        foreach ($data as $dayData) {
            $bigData[$dayData->name][$dayData->store][$dayData->date] = ($dayData->summ) ? $dayData->summ : null;  //Основные данные
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
            foreach ($bigData as & $data_q) {
                foreach ($data_q as & $data_qq) {
                    if (!array_key_exists($date, $data_qq)) {
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
                    $check = false;
                    foreach ($tableData[$name] as $store => $day_data) {   //сверяем с табелем
                        if ($check) break;
                        if (isset($tableData[$name][$store][$date])) $check = $tableData[$name][$store][$date];
                    }
                    if ($check) {
                        $summ = 0;
                        foreach ($data as $d){
                            $summ += $d[$date];
                        }
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