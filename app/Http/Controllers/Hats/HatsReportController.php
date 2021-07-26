<?php

namespace App\Http\Controllers\Hats;


use Carbon\Carbon;

class HatsReportController extends BaseHatsController
{

    public function __construct()
    {
        $this->hats = new HatsController();
    }

    public function create()
    {
        $db_data = $this->hats->get();
        $do_not_format = $this->do_not_format;
        $middle_values = $this->middle_value;
        $header = HatsController::header();
//        $row_names = [
//            'store' => now(),
//            'cashbox' => 'к\'с',
//            'income' => 'д\'с',
//            'cashbox_ret' => 'р\'с',
//            'income_$' => 'д\'\'с',
//            'cashbox_$_1' => 'к\'\'с1',
//            'income_$_1' => 'д\'\'с1',
//            'cashbox_М' => 'к\'\'м',
//            'income_М' => 'д\'\'м',
//            'rent_$' => 'а\'\'',
//            'salary_$' => 'з\'\'',
//            'other_costs' => 'доп расходы',
//            'profit_М' => 'п\'\'м',
//            // Средняя прибыль за месяц отсутствует
//            // Средне-годовая прибыль за месяц отсутствует
//            'cashbox_ret_$_1' => 'р\'\'с1',
//            'cashbox_ret_piece_1' => 'р/к\'\'с1',
//            'discounts' => 'ск\'с',
//            'SUPERINCOME' => 'др\'с',
//            'discounts_1' => 'ск\'с1',
//            'SUPERINCOME_1' => 'др\'с1',
//            'discounts_М' => 'ск\'м',
//            'SUPERINCOME_М' => 'др\'м',
//            'SUPERINCOME_piece_М' => 'др/д м',
//            'items_sold' => 'шт с',
//            'items_sold_1' => 'шт с1',
//            'items_sold_ret' => 'р шт с',
//            'item_sold_ret_piece' => 'р шт/шт с',
//            'items_returned_М' => 'возв шт м',
//            'income_piece_percent_1' => 'д/сс с1',
//            // доля ликвидов от сс
//            'unique_sku' => 'мод',
//            'area' => 'площадь, м2',
//            'profit_by_area' => 'прибыль с 1м2'
//        ];
        $r = request()->get('period');
        $date = (isset($r) and $r < Carbon::now()->format('Y-m-d')) ? $r : Carbon::now()->format('Y-m-d');
        $form = '<form><input  name="period" id="hats-date" type="date" value="' . $date . '"></form>';
        $row_names = [
            'store' => $form,
            'cashbox' => 'Касса',
            'income' => 'Доход',
            'cashbox_ret' => 'Касса(Розница)',
            'income_$' => 'Доход <span class="dollar_symb">$</span>',
            'cashbox_$_1' => 'Касса (c1) <span class="dollar_symb">$</span>',
            'income_$_1' => 'Доход (с1) <span class="dollar_symb">$</span>',
            'cashbox_$_М' => 'Касса (м) <span class="dollar_symb">$</span>',
            'income_$_М' => 'Доход (м) <span class="dollar_symb">$</span>',
            'rent_$' => 'Аренда <span class="dollar_symb">$</span>',
            'salary_$' => 'Зарплата <span class="dollar_symb">$</span>',
            'other_costs_$' => 'Доп расходы <span class="dollar_symb">$</span>',
            'distributed_costs_$' => 'Распред. расходы <span class="dollar_symb">$</span>',
            'profit_$_М' => 'Прибыль (м) <span class="dollar_symb">$</span>',
            // Средняя прибыль за месяц отсутствует
            // Средне-годовая прибыль за месяц отсутствует
            'cashbox_ret_$_1' => 'Касса(розн) (с1) <span class="dollar_symb">$</span>',
            'cashbox_ret_piece_1' => 'Касса(розн) / касса (с1)',
            'discounts' => 'Сумма скидок',
            'SUPERINCOME' => 'СВЕРХДОХОД',
            'discounts_1' => 'Сумма скидок (с1)',
            'SUPERINCOME_1' => 'СВЕРХДОХОД (с1)',
            'discounts_М' => 'Сумма скидок (м)',
            'SUPERINCOME_М' => 'СВЕРХДОХОД (м)',
            'SUPERINCOME_piece_М' => 'Сверхдоход / доход (м)',
            'items_sold_1' => 'Штук продано (с1)',
            'items_sold' => 'Штук продано',
            'items_sold_ret' => 'Штук продано (розн)',
            'item_sold_ret_piece' => 'Шт(розн) / Шт(общ)',
            'items_returned_М' => 'Возвраты (м)',
            'income_piece_percent_1' => 'Доход / СС (с1)',
            // доля ликвидов от сс
            'unique_sku' => 'Модели',
            'area' => 'площадь, м2',
            'profit_by_area' => 'прибыль с 1м2'
        ];

        $head_description_values = [
            'income_rate' => 'Доход / СС',
            'income' => 'Доход',
            'cashbox' => 'Касса',
            'items_sold' => 'Шт. продано'
        ];

        foreach ($row_names as $key => $q) {
            $data[$key] = $db_data[$key];
        }

        $table = '<table id="hats" class="my-table">';
        $table .= '<thead>';

        foreach ($data as $key => $value) {
//            if($key == 'profit_$_М') dd(round(array_sum($value)));
            $table .= '<tr><td class="row_name">' . $row_names[$key] . '</td>';
            if ($key == 'store') $table .= '<td class="td-border-right">Скол</td>';
            elseif (in_array($key, $middle_values)) $table .= '<td class="td-border-right">' . round(array_sum($value) / count($value)) . '&#176;</td>';
            elseif (in_array($key, $do_not_format)) $table .= '<td style="font-family: Tahoma; font-size: 13px" class="td-border-right">' . round(array_sum($value)) . '</td>';
            else $table .= '<td class="td-border-right">' . $this->bigAndSmall(round(array_sum($value))) . '</td>';

            foreach ($value as $v) {
                $v = round($v);
                if ($key == 'store') $table .= '<td class="store">' . $v . '</td>';
                elseif (in_array($key, $middle_values)) $table .= '<td>' . $v . '&#176;</td>';
                elseif (in_array($key, $do_not_format)) $table .= '<td style="font-family: Tahoma; font-size: 13px">' . $v . '</td>';
                else $table .= '<td>' . $this->bigAndSmall($v) . '</td>';
            }
            $table .= '</tr>';
            if ($key == 'store') $table .= '</thead>';
        }
        $table .= '</table>';


        // HEADER TABLE
        $table_header = '<table>';
        foreach ($header as $key => $value) {

            $table_header .= '<tr>';
            $table_header .= '<td class="head_description">' . $head_description_values[$key] . '</td>';
            foreach ($value as $k => $val) {
                if (Carbon::parse($k)->format('m') < 7) {
                    $bg = 'grey';
                } else {
                    $bg = 'notgrey';
                }
                if ($key == 'income_rate') {
                    $table_header .= '<td class="' . $bg . '">' . round($val) . '&#176;</td>';
                } else {
                    $table_header .= '<td class="' . $bg . '">' . round($val) . '</td>';
                }
            }
            $table_header .= '</tr>';
        }
        $table_header .= '</table>';


        return view('hats.index', compact('table', 'table_header'));
    }
}