<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Hats;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class DownloadHatsData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dates;

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
        $data = $this->get('Header_Headlines');
        $data = $this->trimCity($data);
        $data = $this->trimStores($data);
        $data = $this->prepareData($data);
        $this->insert($data);


    }

    public function insert($insert)
    {
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

    public function get($filename)
    {
        $xls = IOFactory::load(base_path('storage/app/public/dist/' . $filename . '.xlsx'));
        $data = $xls->getActiveSheet()->toArray();
        return $data;
    }

    public function trimCity($data)
    {
        $data2 = array();

        $dates = array_splice($data[8], 2);
        $this->dates = $dates;
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i][1] == 'Харьков') {
                $j = $i + 1;
                while ($data[$j][1] != 'Хмельницкий') {
                    $data2[] = $data[$j];
                    $j++;
                }
            }
        }
        return $data2;
    }

    public function trimStores($data2)
    {
        $data3 = array();

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
        return $data3;
    }

    public function prepareData($data3)
    {
        $dates = $this->dates;
        $data4 = array();
        $data5 = array();
        $insert = array();

        foreach ($data3 as $key => $value) {
            foreach ($value as $k => $val) {
                $data4[$key][$val[1]] = array_splice($val, 2);
            }
        }
        foreach ($data4 as $key => $value) {
            foreach ($value as $k => $val) {
                foreach ($val as $kk => $v) {
                    if ($v == '#NULL!') $v = 0;
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
        return $insert;
    }

}
