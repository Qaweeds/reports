<?php

namespace App\Http\Controllers\Hats;


use App\Models\Hats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HatsController extends BaseHatsController
{
    protected $date;
    protected $stores;

    public function __construct()
    {
//        dd(request()->all());
        $date = request()->get('period');
        if (isset($date) and $date < Carbon::now()->format('Y-m-d')) $this->date = Carbon::parse($date);
        else $this->date = Carbon::now()->subDay();

    }


    public function get()
    {
        $today = $this->getTodayData();
        $from_first = $this->getFromFirstDate();
        $thirty = $this->getThirtyDays();
        $header = $this->header();
        $data = array_merge($today, $from_first, $thirty);

        return $data;

    }

    public function getTodayData()
    {
//        $date = Carbon::now()->subDay()->format('Y-m-d');
        $date = $this->date->format('Y-m-d');
        $data = DB::select('SELECT  store,
                                          cashbox,
                                          cashbox_ret,
                                          income,
                                          income / dollar_rate as income_$,
                                          discounts,
                                          SUPERINCOME,
                                          items_sold, items_sold_ret,
                                          (items_sold_ret / items_sold * 100) as item_sold_ret_piece,
                                          unique_sku,
                                          rent  as rent_$,
                                          salary as salary_$,
                                          other_costs as other_costs_$,
                                          distributed_costs as distributed_costs_$,
                                          area,
                                          profit / area as profit_by_area
                                  FROM `hats` WHERE date = \'' . $date . '\' order by store');
        foreach ($data as &$d) {
            $d = (array)$d;
        }

        foreach ($data as $store) {
            foreach ($store as $key => $val) {
                $arr[$key][] = $val;
            }
        }
        $this->stores = $arr['store'];
        return $arr;
    }

    public static function header()
    {
        $date_start = DB::table('hats')->min('date');
        $date_end = DB::table('hats')->max('date');
        $monthes = Carbon::parse($date_end)->diffInMonths($date_start);
        $date = Carbon::now();
//        dd($date_start);

        for ($i = 1; $i <= $monthes; $i++) {
            $end = $date->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d');
            $start = $date->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d');
            $select = DB::table('hats')
                ->selectRaw(
                    "(Sum(income_piece) / COUNT(income_piece)) as income_rate,
                                Sum(income) as income,
                                Sum(cashbox) as cashbox,
                                Sum(items_sold) as items_sold")
                ->whereBetween('date', [$start, $end])->first();

            $data['income_rate'][$start] = $select->income_rate;
            $data['income'][$start] = $select->income;
            $data['cashbox'][$start] = $select->cashbox;
            $data['items_sold'][$start] = $select->items_sold;
        }
        return $data;
    }

    public function getFromFirstDate()
    {
        $date = $this->date;
        $end = $date->copy()->format('Y-m-d');
        $start = $date->copy()->firstOfMonth()->format('Y-m-d');
        $period = '_1';
        $stores = implode(', ', $this->stores);
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
                                FROM `hats` WHERE date BETWEEN \'' . $start . '\' and \'' . $end . '\' and
                                store in (' . $stores . ')
                                GROUP BY store order by store');
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
        $date = $this->date;
        $end = $date->copy()->format('Y-m-d');
        $start = $date->copy()->subMonth()->format('Y-m-d');
        $period = '_М';
        $stores = implode(', ', $this->stores);
        // SUPERINCOME_piece  -- доля супердохода от дохода

        $data = DB::select('SELECT
                                    Sum(cashbox / dollar_rate) as cashbox_$' . $period . ',
                                    Sum(income / dollar_rate) as income_$' . $period . ',
                                    Sum(profit / dollar_rate) as profit_$' . $period . ',
                                    Sum(discounts) as discounts' . $period . ',
                                    Sum(SUPERINCOME) as SUPERINCOME' . $period . ',
                                    (Sum(SUPERINCOME) / Sum(income) *100) as SUPERINCOME_piece' . $period . ',
                                    Sum(items_returned) as items_returned' . $period . '
                                FROM `hats` WHERE date BETWEEN \'' . $start . '\' and \'' . $end . '\'and
                                store in (' . $stores . ')
                                GROUP BY store order by store');

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
