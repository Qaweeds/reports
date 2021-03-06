<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\DistributorsData;
use App\Models\DistributorsRetail;
use App\Models\DistributorTimesheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class DistributorController extends Controller
{
    private $cities = array('Харьков', 'Одесса', 'Хмельницкий', 'ИМ');
    private $i_store = array('Корх Игорь Григорьевич ');
    private $stores = array('30 Харьков, Inter-701 (175.175А, 175В)', '35 Харьков, Inter-701 (175D)', '81 Харьков, 206А, 206В', '91 Харьков, 21-02-291');

    public function get($filename)
    {
        $xls = IOFactory::load(base_path('storage/app/public/dist/' . $filename . '.xlsx'));
        $range = 'A1:' . $xls->getActiveSheet()->getHighestRowAndColumn()['column'] . $xls->getActiveSheet()->getHighestRowAndColumn()['row'];
        $xls = $xls->getActiveSheet()->rangeToArray($range);
        return $xls;
    }

    public function sort($data)
    {
        $arr = array();
        $arr2 = array();
        $excel = $data;
        for ($i = 0; $i < count($excel); $i++) {
            array_pop($excel[$i]);
            array_shift($excel[$i]);
            $arr[] = $excel[$i];
        }
        for ($i = 1; $i < count($arr); $i++) {
            $arr2[] = array_combine($arr[0], $arr[$i]);

        }
        for ($i = 0; $i < count($arr2); $i++) {
            foreach ($arr2[$i] as $key => $q) {
                if ($q == '#NULL!') $arr2[$i][$key] = '';
            }
        }
        return $arr2;
    }

    public function trimData($data)
    {
        $city = array('Одесса', 'Харьков', 'Хмельницкий');
        for ($j = 0; $j < count($data); $j++) {
            if ($data[$j][1] == 'Подразделение.Регион') {
                $dates = $data[$j]; // Массив с датами
                break;
            }
        }
        for ($i = 0; $i < count($data); $i++) {
            if (in_array($data[$i][1], $city)) {
                array_splice($data, 0, $i + 1);
                array_unshift($data, $dates);
                break;
            }
        }
        array_pop($data); // Удалить Итог
        return $data;
    }

    public function slice($data)
    {
        $arr = $data;
        $arr2 = array();
        $p = '/^[а-я]+/ui';
        for ($i = 0; $i < count($arr); $i++) {
            if (preg_match($p, $arr[$i]['Подразделение.Регион']) and $arr[$i]['Подразделение.Регион'] != 'Офис' and $arr[$i]['Подразделение.Регион'] != 'СайтС') {
                $j = $i + 1;
                while ($j < count($arr) and (!preg_match($p, $arr[$j]['Подразделение.Регион']) or $arr[$j]['Подразделение.Регион'] == 'Офис' or $arr[$j]['Подразделение.Регион'] == 'СайтС')) {
                    $arr2[$arr[$i]['Подразделение.Регион']][$arr[$j]['Подразделение.Регион']] = $arr[$j];
                    $j++;
                }
            }
        }
        return $arr2;
    }

    public function trimForStaff($data)
    {
        $cities = $this->cities;
        array_splice($data, 0, 2);

        for ($i = 0; $i < count($data); $i++) {
            if (in_array($data[1][1], $cities)) {
                for ($j = 0; $j < count($data); $j++) {
                    if (in_array($data[$j][1], $cities)) {
                        array_splice($data, $j, 1);
                    }
                }
                break;
            }
            array_splice($data, 1, 1);
        }
        for ($k = 0; $k < count($data); $k++) {
            if (!is_null(max($data[$k]))) {
                $arr[] = $data[$k];
            }
        }
        return $arr;
    }

    public function prepareDistributorsStaff($data)
    {
        $date = Carbon::parse(explode(':', $data[0][1])[1])->format('Y-m-d');

        for ($i = 1; $i < count($data); $i++) {
            if ($i % 2) {
                if (in_array($data[$i][2], $this->i_store)) $arr[$data[$i][2]] = 'СайтС';
                else $arr[$data[$i][2]] = $data[$i + 1][2];
            }
        }
        return array('data' => $arr, 'date' => $date);
    }

    public function update_primary(){
        $data = $this->get('Sales_Distributors');
        $data = $this->trimData($data);
        $data = $this->sort($data);
        $data = $this->slice($data);

        $rangeEnd = Carbon::now()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays(45)->format('Y-m-d');
        $stores = $this->stores;

        if (isset($data['Харьков'])) unset($data['Харьков']); //Убрать общие цифры по харькову
        $i = 0;

        foreach ($stores as $store) {
            Distributor::where('store', $store)->whereBetween('date', array($rangeStart, $rangeEnd))->delete();
        }
        foreach ($data as $name => $value) {
            foreach ($value as $store => $val) {
                foreach ($val as $date => $summ) {
                    if ($date != 'Подразделение.Регион') {
                        if (isset($store) and $store == 'Офис') $store .= ' Харьков 120';
                        $i++;
                        Distributor::updateOrCreate(
                            [
                                'name' => $name,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'store' => $store
                            ],
                            [
                                'summ' => (int)str_replace(',', '', strtok($summ, '.'))
                            ]
                        );
                    }
                }
            }
        }

        echo 'done' . $i;
    }

    public function updateDistributors()
    {
        set_time_limit(300);
        $this->updateDistributorsTimeSheet();
        $this->updateDistributorsStaff();
        $this->updateDistributorsRetail();
        $this->update_primary();
        $this->updateSuperStores();

    }

    public function updateDistributorsTimeSheet()    //Табель
    {
        set_time_limit(100);
        $data = $this->get('UniRepWorkingTime');
        $data = $this->trimData($data);
        $data = $this->sort($data);
        $data = $this->slice($data);

        if (isset($data['Харьков'])) unset($data['Харьков']); //Убрать общие цифры по харькову
        $i = 0;
        foreach ($data as $name => $value) {
            foreach ($value as $store => $val) {
                foreach ($val as $date => $work) {
                    if ($date != 'Подразделение.Регион') {
                        if (isset($store) and $store == 'Офис') $store .= ' Харьков 120';
                        $i++;
                        DistributorTimesheet::updateOrCreate(
                            [
                                'name' => $name,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'store' => $store
                            ],
                            [
                                'work' => (int)$work
                            ]
                        );
                    }
                }
            }
        }
        echo ' tabel' . $i;
    }

    public function updateDistributorsStaff()   //Кадры
    {
        $data = $this->get('StaffRepListWorkingEmployee_StaffReportListWorkingEmp');

        $data = $this->trimForStaff($data);
        $data_and_date = $this->prepareDistributorsStaff($data);  //return Array;
        $data = $data_and_date['data'];
        $date = $data_and_date['date'];
        $i = 0;
        foreach ($data as $name => $store) {

            $i++;
            DistributorsData::updateOrCreate(
                [
                    'name' => $name,
                    'date' => $date,
                    'main_store' => $store
                ]
            );
        }
        echo ' stuff' . $i;
    }

    public function updateSuperStores()
    {
        set_time_limit(200);
        $rangeEnd = Carbon::now()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays(45)->format('Y-m-d');
        $stores = $this->stores;
        $dates = DistributorTimeSheet::distinct()->whereBetween('date', array($rangeStart, $rangeEnd))->orderByDesc('date')->pluck('date')->toArray();
        foreach ($stores as $store) {
            foreach ($dates as $date) {
                $names = DistributorTimeSheet::where('store', $store)->where('work', 1)->where('date', $date)->pluck('name');
                if (count($names)) {
                    $summ = Distributor::where('store', $store)->where('date', $date)->sum('summ') / count($names);
                    Distributor::where('store', $store)->where('date', $date)->delete();
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

    public function updateDistributorsRetail()
    {
        set_time_limit(100);
        $data = $this->get('Sales_Distributors2');
        $data = $this->trimData($data);
        $data = $this->sort($data);
        $data = $this->slice($data);

        $rangeEnd = Carbon::now()->format('Y-m-d');
        $rangeStart = Carbon::parse($rangeEnd)->subDays(45)->format('Y-m-d');
        $stores = $this->stores;

        if (isset($data['Харьков'])) unset($data['Харьков']); //Убрать общие цифры по харькову
        $i = 0;

        foreach ($stores as $store) {
            DistributorsRetail::where('store', $store)->whereBetween('date', array($rangeStart, $rangeEnd))->delete();
        }
        foreach ($data as $name => $value) {
            foreach ($value as $store => $val) {
                foreach ($val as $date => $summ) {
                    if ($date != 'Подразделение.Регион') {
                        if (isset($store) and $store == 'Офис') $store .= ' Харьков 120';
                        $i++;
                        DistributorsRetail::updateOrCreate(
                            [
                                'name' => $name,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'store' => $store
                            ],
                            [
                                'summ' => (int)str_replace(',', '', strtok($summ, '.'))
                            ]
                        );
                    }
                }
            }
        }
        echo ' retail' . $i;
    }
}
