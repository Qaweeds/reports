<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportController extends BaseDistibutorsController
{

    protected $do_not_show = [
        'Багдасарова Светлана Яковлевна',
        'Ковалева Алина Олеговна (осн.)',
        'Ковресов Владислав Константинович (осн.)',
        'Окопная Юлия Вадимовна (осн.)',
        'Темная Анна Игоревна',
        'Титкова Анна Вячеславовна'
    ];

    public function index(Request $r)
    {

        $rule = array('D' => 'sometimes | date | after_or_equal:2021-03-01');
        $v = Validator::make($r->input(), $rule);
        if ($v->fails()) {
            return view('report.wrong')->with(Session::flash('date-flip', 'Укажите дату после 3 Марта 2021 года'));
        }

        $data['table'] = $this->create_table($r);
        $data['names'] = $this->names;

        return view('report.index', $data);

    }

    public function create_table(Request $r)
    {
        $sort = $r->get('sort');
        $date = $r->get('D');

        $data = DistributorDataController::getData($date);   // вывод из базы
        $data = $this->addStore($data);
        $data = $this->addMiddle($data);

        if (isset($sort)) {
            $new_data[] = $data[0];
            switch ($sort) {
                case 'q':
                    $keys = $this->getKeysForSort($data, 3);
                    $data = $this->shuffle_slice($keys, $data, $new_data);
                    break;
                case 'm':
                    $keys = $this->getKeysForSort($data, 0);
                    $data = $this->shuffle_store($keys, $data, $new_data);
                    break;
            }
        }

        $t = '<table class="table-hover  my-table table_sort" >';  // основная таблица;
        $pattern = '/^[а-я]+/ui';
        $ruMonths = ['', 'Январь', "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
        $weekcolor = '';
        $summ_by_period = array();
        $weeks_nums = array();

        $firstDate = Carbon::parse($data[0][4]);
        $summ_by_period['month'] = $this->totalByPeriod($firstDate, 'month');
        $summ_by_period['season'] = $this->totalByPeriod($firstDate, 'season');
        $summ_by_period['year'] = $this->totalByPeriod($firstDate, 'year');
        $j = 0;

        for ($i = 0; $i < count($data); $i++) {

            if (in_array($data[$i][1], $this->do_not_show)) continue;

            $last_key = count($data[$i]) - 1;
            $firstDateW = $firstDate;
            $name_row = preg_match($pattern, $data[$i][1]);
            $second_magaz = '';
            $start_row = '';
            $name = $data[$i][1];

            if ($i == 0) { // если шапка таблицы
                $t .= '<thead class="table-head"><tr><td class="places-cell"></td>';
            } else {
                if ($i == 1) {
                    $t .= '<tbody>';
                }
                if ($name_row && $name != 'Офис Харьков 120' and $name != "СайтС" and $data[$i][3] != null) { // если ряд с именем
                    if (isset($data[$i + 2]) and !preg_match($pattern, $data[$i + 2][1])) $second_magaz = $data[$i + 2];
                    $j++;
                    $t .= '<tr class="name-tr"><td class="places-cell">' . $j . '</td>';
                } else {
                    continue;
                }
            }
            foreach ($data[$i] as $key => $cell) {

                if ($cell == 'null') $cell = '';

                /* Проверка на шапку и тело */
                if ($i == 0) {
                    if ($key == 0) $cell = 'M';
                    else if ($key == 1) $cell = 'ФИО';
                    else if ($key == 2) $cell = '<div class="date-wrap no-events">Тренд</div>';
                    else if ($key == 3) $cell = '<div class="date-wrap no-events">Скол</div>';
                }

                if ($key > 3 and $key != $last_key) {
                    $weekcolor = ((int)Carbon::parse($data[0][$key])->format('W') % 2) ? 'notgrey' : 'grey';  // класс для разделения ПОНЕДЕЛЬНО

                    if (Carbon::parse($firstDate)->format('N') == 7 and isset($start_row)) {   //если Воскресенье - последний день в таблице
                        unset($start_row);
                        $firstDateW = Carbon::parse('monday'); //просто заглушка
                    }

                    if ($i == 0) {
                        if ($firstDateW->format('W') != Carbon::parse($data[0][$key])->format('W')) {
                            $firstDateW = Carbon::parse($data[0][$key]);
                            $weeks_nums[] = $firstDateW->format('W');
                            $summ_by_period[$firstDateW->format('W')] = $this->totalByPeriod($firstDateW->copy(), 'week');
                            $t .= '<td class="sort week-bg date"><div class="date-wrap week-head no-events">Неделя</div></td>';
                        }
                    } else {
//                        dd($summ_by_period);
                        if ($firstDateW->format('W') != Carbon::parse($data[0][$key])->format('W')) {
                            $firstDateW = Carbon::parse($data[0][$key]);
                            $totalW = (isset($summ_by_period[$firstDateW->format('W')][$name])) ? $summ_by_period[$firstDateW->format('W')][$name] : 0;
                            $t .= ($i == 0) ? '<td class="sort week-bg date ' . $weekcolor . '"> <div class="date-wrap week-head no-events">Неделя</div>   </td>' :
                                '<td class="week-bg ' . $weekcolor . '">' . $this->bigAndSmall($totalW) . '</td>';
                        }
                    }
                }
                /* --------------------------------------*/
                if ($key < 4) {
                    switch ($key) {
                        case '0':
                            $t .= ($i) ? '<td class="main-store">' . mb_substr($cell, 0, 2) . '</td>' : '<td class="store-head notgrey bg-white sort">' . $cell . '</td>';
                            break;
                        case '1':
                            $t .= ($i) ? '<td class="name">' . $this->trimName($data[$i]) . '</td>' : '<td class="fio-head notgrey bg-white sort">' . $cell . '</td>';
                            break;
                        case '2':
                            $t .= ($i) ? '<td class="trend">trendrow' . $i . '</td>' : '<td class="date notgrey trend sort">' . $cell . '</td>';
                            break;
                        case '3':
                            $t .= ($i) ? '<td class="slice">' . $this->bigAndSmall($cell) . '</td>' : '<td class="slice date notgrey sort">' . $cell . '</td>';
                            break;
                    }
                } else if ($key != $last_key) { // проверка на последний столб
                    if ($i == 0) {
                        $t .= '<td class="date ' . $weekcolor . '"><div class="date-wrap">' . $this->trimDate($cell) . '</div></td>';
                    } else if (is_array($second_magaz) and $key < count($second_magaz) and $cell != null and $cell != 'null') {
                        $k = $i + 1;
                        $all_stores = array();
                        $summ_of_two = false;
                        while (isset($data[$k]) and !preg_match($pattern, $data[$k][1])) {
                            $all_stores[] = $data[$k];
                            $k++;
                        }
                        foreach ($all_stores as $store) {
                            if ($cell == $store[$key]) {
                                if (substr($store[1], 0, 2) == substr($data[$i][0], 0, 2)) {
                                    $t .= '<td class="2 ' . $weekcolor . '">' . $this->bigAndSmall($cell) . '</td>';
                                } else {
                                    $t .= '<td class="1 ' . $weekcolor . '"><small class="mini-magazin">' . substr($store[1], 0, 2) . '</small>' .
                                        $this->bigAndSmall($cell) . '</td>';
                                }
                                $summ_of_two = true;
                                break;
                            }
                        }
                        if (!$summ_of_two) $t .= '<td class="2 ' . $weekcolor . '">' . $this->bigAndSmall($cell) . '</td>';
                    } else {
                        $t .= '<td class="' . $weekcolor . '">' . $this->bigAndSmall($cell) . '</td>';
                    }
                }
            }
            /*------------------------------- СТАТИСТИКА МЕСЯЦ, СЕЗОН, ГОД. ТРЕНДЫ  --*/
            if ($i == 0) {
                $t .= '<td class="current-month-head date sort total-data-border"><div class="date-wrap no-events">' . $ruMonths[Carbon::parse($firstDate)->subMonth()->format('n')] .
                    '</div></td>';
                $t .= '<td class="current-season-head date sort"><div class="date-wrap no-events">Сезон</div></td>';
                $t .= '<td class="current-year-head date sort"><div class="date-wrap no-events">Год</div></td>';

                $t .= "</tr></thead>";
            } else {
//                $w3 = isset($summ_by_period[$weeks_nums[2]][$name]) ? $summ_by_period[$weeks_nums[2]][$name] : 0;
//                $w4 = isset($summ_by_period[$weeks_nums[3]][$name]) ? $summ_by_period[$weeks_nums[3]][$name] : 0;
                if (count($weeks_nums) != 4 or (
                        isset($summ_by_period[$weeks_nums[2]][$name]) and
                        isset($summ_by_period[$weeks_nums[3]][$name]) and
                        $summ_by_period[$weeks_nums[2]][$name] + $summ_by_period[$weeks_nums[3]][$name] == 0
                    ) or (
                        !isset($summ_by_period[$weeks_nums[2]][$name]) and !isset($summ_by_period[$weeks_nums[3]][$name])
                    )) $trend = '';  //ТРЕНДЫ
                else {
                    $weeek_1 = (isset($summ_by_period[$weeks_nums[0]][$name])) ? $summ_by_period[$weeks_nums[0]][$name] : 0;
                    $weeek_2 = (isset($summ_by_period[$weeks_nums[1]][$name])) ? $summ_by_period[$weeks_nums[1]][$name] : 0;
                    $weeek_3 = (isset($summ_by_period[$weeks_nums[2]][$name])) ? $summ_by_period[$weeks_nums[2]][$name] : 0;
                    $weeek_4 = (isset($summ_by_period[$weeks_nums[3]][$name])) ? $summ_by_period[$weeks_nums[3]][$name] : 0;
                    $trend = round(($weeek_1 + $weeek_2) / ($weeek_3 + $weeek_4) * 100) . '&#176;';
                }
                $t = str_replace('trendrow' . $i, $trend, $t);

                $t .= '<td class="current-month-summ total-data-border">';
                $t .= (isset($summ_by_period['month'][$name])) ? $this->bigAndSmall($summ_by_period['month'][$name]) : 0;
                $t .= '</td>';

                $t .= '<td class="current-month-summ">';
                $t .= (isset($summ_by_period['season'][$name])) ? $this->bigAndSmall($summ_by_period['season'][$name]) : 0;
                $t .= '</td>';

                $t .= '<td class="current-month-summ">';
                $t .= (isset($summ_by_period['year'][$name])) ? $this->bigAndSmall($summ_by_period['year'][$name]) : 0;
                $t .= '</td>';

                $t .= '</tr>';
            }
        }
        $t .= '</tbody></table>';

        return $t;
    }
}
