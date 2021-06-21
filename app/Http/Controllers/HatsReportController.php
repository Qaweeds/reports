<?php

namespace App\Http\Controllers;


class HatsReportController extends Controller
{
    protected $hats;
    protected $do_not_format = [
        'unique_sku',
        'item_sold_ret_piece',
        'income_piece_percent_1',
        'cashbox_ret_piece_1',
        'store',
        'items_returned_М',
        'items_sold',
        'items_sold_1',
        'items_sold_ret',
        'area',
        'SUPERINCOME_piece_М',
    ];
    protected $middle_value = [
        'cashbox_ret_piece_1',
        'SUPERINCOME_piece_М',
        'profit_by_area',
        'item_sold_ret_piece',
        'income_piece_percent_1',
    ];

    public function __construct()
    {
        $this->hats = new HatsController();
    }


    public function create()
    {
        $get = $this->hats->get();
        $do_not_format = $this->do_not_format;
        $middle_values = $this->middle_value;
        $table = '<table id="hats" class="my-table mt-5 ml-5">';
        $row_names = [
            'store' => now(),
            'cashbox' => 'к\'с',
            'income' => 'д\'с',
            'cashbox_ret' => 'р\'с',
            'income_$' => 'д\'\'с',
            'cashbox_$_1' => 'к\'\'с1',
            'income_$_1' => 'д\'\'с1',
            'cashbox_М' => 'к\'\'м',
            'income_М' => 'д\'\'м',
            'rent_$' => 'а\'\'',
            'salary_$' => 'з\'\'',
            'other_costs' => 'доп расходы',
            'profit_М' => 'п\'\'м',
            // Средняя прибыль за месяц отсутствует
            // Средне-годовая прибыль за месяц отсутствует
            'cashbox_ret_$_1' => 'р\'\'с1',
            'cashbox_ret_piece_1' => 'р/к\'\'с1',
            'discounts' => 'ск\'с',
            'SUPERINCOME' => 'др\'с',
            'discounts_1' => 'ск\'с1',
            'SUPERINCOME_1' => 'др\'с1',
            'discounts_М' => 'ск\'м',
            'SUPERINCOME_М' => 'др\'м',
            'SUPERINCOME_piece_М' => 'др/д м',
            'items_sold' => 'шт с',
            'items_sold_1' => 'шт с1',
            'items_sold_ret' => 'р шт с',
            'item_sold_ret_piece' => 'р шт/шт с',
            'items_returned_М' => 'возв шт м',
            'income_piece_percent_1' => 'д/сс с1',
            // доля ликвидов от сс
            'unique_sku' => 'мод',
            'area' => 'площадь, м2',
            'profit_by_area' => 'прибыль с 1м2'
        ];

        foreach ($row_names as $key => $q) {
            $data[$key] = $get[$key];
        }

        foreach ($data as $key => $value) {
            $table .= '<tr><td class="row_name">' . $row_names[$key] . '</td>';
            if ($key == 'store') $table .= '<td class="td-border-right">Общее</td>';
            elseif (in_array($key, $middle_values)) $table .= '<td class="td-border-right">' . round(array_sum($value) / count($value)) . '&#176;</td>';
            elseif (in_array($key, $do_not_format))$table .= '<td class="td-border-right">' . round(array_sum($value)) . '</td>';
            else $table .= '<td class="td-border-right">' . $this->bigAndSmall(round(array_sum($value))) . '</td>';

            foreach ($value as $v) {
                $v = round($v);
                if ($key == 'store') $table .= '<td class="store">' . $v . '</td>';
                 elseif (in_array($key, $do_not_format)) $table .= '<td>' . $v . '</td>';
                 else $table .= '<td>' . $this->bigAndSmall($v) . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</table>';

        return view('hats.index', array('table' => $table));
    }
}
