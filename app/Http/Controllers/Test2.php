<?php

namespace App\Http\Controllers;


use App\Models\Hats;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Test2 extends Controller
{
    public function __invoke()
    {
        $xls = IOFactory::load(base_path('storage/app/public/dist/Header_Headlines.xlsx'));
        $data = $xls->getActiveSheet()->toArray();
        // trim Харьков
        $dates = array_splice($data[8], 2);
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i][1] == 'Харьков') {
                $j = $i + 1;
                while ($data[$j][1] != 'Хмельницкий') {
                    $data2[] = $data[$j];
                    $j++;
                }
            }
        }
        // trim stores
        $pattern = '/^\d/';
        for ($i = 0; $i < count($data2); $i++) {
            if (preg_match($pattern, $data2[$i][1])) {
                $j = $i + 1;
                while (isset($data2[$j]) and !preg_match($pattern, $data2[$j][1])) {
                    $data3[$data2[$i][1]][] = $data2[$j];
                    $j++;
                }
            }
        }

        foreach ($data3 as $key => $value) {
            foreach ($value as $k => $val) {
                $data4[$key][$val[1]] = array_splice($val, 2);
            }
        }
        foreach ($data4 as $key => $value) {
            foreach ($value as $k => $val) {
                foreach ($val as $kk => $v) {
                    if($v == '#NULL!') $v = 0;
                    else $v = (float)str_replace(',', '', $v);
                    $data5[$key][$k][$dates[$kk]] = $v;
                }
            }
        }

        foreach ($data5 as $store => $data) {
            foreach ($dates as $date) {
                foreach ($data as $field => $day_data) {
                    $insert[$store][$date]['store'] = $store;
                    $insert[$store][$date]['date'] = Carbon::parse($date)->format('Y-m-d');
                    $insert[$store][$date][$field] = $day_data[$date];
                }
            }
        }
//        dd($insert);
        Hats::truncate();
        foreach ($insert as $store => $data) {
            foreach ($data as $d) {
                Hats::updateOrCreate(
                    [
                        'store' => $d['store'],
                        'date' => $d['date']
                    ],
                    [
                        'cashbox' => $d['Касса (общая)'],
                        'cashbox_ret' => $d['Касса (розница)'],
                        'income' => $d['Доход'],
                        'profit' => $d['Прибыль'],
                        'income_piece' => $d['Доля дохода'],
                        'rent' => $d['Аренда'],
                        'salary' => $d['Зарплата'],
                        'other_costs' => $d['Доп. расходы'],
                        'discounts' => $d['Общая сумма предоставленных скидок'],
                        'items_sold' => $d['Количество продаж'],
                        'items_sold_ret' => $d['Количество продаж в розницу'],
                        'items_returned' => $d['Количество возвратов'],
                        'unique_sku' => $d['Количество уникальных SKU на магазине'],
                        'area' => $d['Количество возвратов'],
                        'dollar_rate' => $d['Курс $ к гривне'],
                        'SUPERINCOME' => $d['Сверхдоход'],
                    ]
                );
            }
        }



    }
}
