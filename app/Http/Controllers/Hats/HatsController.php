<?php

namespace App\Http\Controllers\Hats;



use App\Models\Hats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HatsController extends BaseHatsController
{
    public function store()
    {
        $date = Carbon::now();

        $square = [
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
             6=> '',
             7=> '',
             8=> '',
             9=> '',
             10=> '',
             11=> '',
             12=> '',
             13=> '',
             14=> '',
             15=> '',
             16=> '',
             17=> '',
             18=> '',
             19=> '',
             20=> '',
        ];

        for ($store = 1; $store < 21; $store++) {
            for ($i = 1; $i < 45; $i++) {
                Hats::updateOrCreate(
                    [
                        'store' => $store,
                        'date' => $date->copy()->subDays($i)->format('Y-m-d')
                    ],
                    [
                        'cashbox' => rand(1, 1000) + $i,
                        'cashbox_ret' => rand(1, 1000) + $i,
                        'income' => rand(1, 1000) + $i,
                        'profit' => rand(1, 1000) + $i,
                        'income_piece' => rand(1, 1000) + $i,
                        'rent' => rand(1, 1000) + $i,
                        'salary' => rand(1, 1000) + $i,
                        'other_costs' => rand(1, 1000) + $i,
                        'discounts' => rand(1, 1000) + $i,
                        'items_sold' => rand(1, 1000) + $i,
                        'items_sold_ret' => rand(1, 1000) + $i,
                        'items_returned' => rand(1, 1000) + $i,
                        'unique_sku' => rand(1, 1000) + $i,
                        'area' => rand(1, 1000) + $i,
                        'dollar_rate' => rand(1, 1000) + $i,
                        'SUPERINCOME' => rand(1, 1000) + $i,
                    ]
                );
            }
        }
    }

    public function get()
    {
        $today = $this->getTodayData();
        $from_first = $this->getFromFirstDate();
        $thirty = $this->getThirtyDays();
        $data = array_merge($today, $from_first, $thirty);

        return $data;

    }
    public function getTodayData()
    {
        $date = Carbon::now()->subDays(10)->format('Y-m-d');
        $data = DB::select('SELECT store, 
                                          cashbox, 
                                          cashbox_ret, 
                                          income, 
                                          income / dollar_rate as income_$, 
                                          discounts, 
                                          SUPERINCOME,
                                          items_sold, items_sold_ret, 
                                          (items_sold_ret / items_sold * 100) as item_sold_ret_piece, 
                                          unique_sku, rent / dollar_rate as rent_$,
                                          salary / dollar_rate as salary_$, 
                                          other_costs, area, profit / area as profit_by_area
                                  FROM `hats` WHERE date = \'' . $date . '\' ');

        foreach ($data as &$d) {
            $d = (array)$d;
        }

        foreach ($data as $store) {
            foreach ($store as $key => $val) {
                $arr[$key][] = $val;
            }
        }
        return $arr;
    }

    public function getFromFirstDate()
    {
        $date = Carbon::now();
        $start = $date->copy()->firstOfMonth();
        $period = '_1';

        // cashbox_ret_piece  --доля розничеой кассы к кассе
        // income_piece_percent  --доля дохода от себестоимости

        $data = DB::select('SELECT 
                                    Sum(cashbox / dollar_rate) as cashbox_$' . $period . ',
                                    Sum(income / dollar_rate) as income_$' . $period . ',
                                    Sum(cashbox_ret / dollar_rate) as cashbox_ret_$' . $period . ',   
                                    (Sum(cashbox_ret) / Sum(cashbox) *100) as cashbox_ret_piece' . $period . ',
                                    Sum(discounts) as discounts' . $period . ',
                                    Sum(SUPERINCOME) as SUPERINCOME' . $period . ',
                                    Sum(items_sold) as items_sold' . $period . ',
                                    (Sum(income_piece) / COUNT(income_piece)) as income_piece_percent' . $period . '
                                FROM `hats` WHERE date BETWEEN \'' . $start . '\' and \'' . $date . '\' 
                                GROUP BY store');
        foreach ($data as &$d) {
            $d = (array)$d;
        }
        foreach ($data as $store) {
            foreach ($store as $key => $val) {
                $arr[$key][] = $val;
            }
        }
        return $arr;
    }

    public function getThirtyDays()
    {
        $date = Carbon::now();
        $start = $date->copy()->subDays(30);
        $period = '_М';

        // SUPERINCOME_piece  -- доля супердохода от дохода

        $data = DB::select('SELECT 
                                    Sum(cashbox / dollar_rate) as cashbox_$' . $period . ',
                                    Sum(income / dollar_rate) as income_$' . $period . ',
                                    Sum(profit / dollar_rate) as profit_$' . $period . ',
                                    Sum(discounts) as discounts' . $period . ',
                                    Sum(SUPERINCOME) as SUPERINCOME' . $period . ',
                                    (Sum(SUPERINCOME) / Sum(income) *100) as SUPERINCOME_piece' . $period . ',
                                    Sum(items_returned) as items_returned' . $period . '              
                                FROM `hats` WHERE date BETWEEN \'' . $start . '\' and \'' . $date . '\' 
                                GROUP BY store');

        foreach ($data as &$d) {
            $d = (array)$d;
        }

        foreach ($data as $store) {
            foreach ($store as $key => $val) {
                $arr[$key][] = $val;
            }
        }
        return $arr;
    }
}
