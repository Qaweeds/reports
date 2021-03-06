<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\DistributorsData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class ReportController extends Controller
{
    private $keys;
    private $shuffleData;
    private $shuffled;
    private $names;
    private $do_not_show = [
        'Багдасарова Светлана Яковлевна ',
        'Ковалева Алина Олеговна (осн.)',
        'Ковресов Владислав Константинович (осн.)',
        'Окопная Юлия Вадимовна (осн.)',
        'Темная Анна Игоревна ',
        'Титкова Анна Вячеславовна '
    ];

    public function auth(Request $r)
    {
        if (isset($r->D) and ($r->D > Carbon::now() or $r->D < '2021-03-01')) {
            Session::flash('date-flip', 'Начальная дата не может быть больше, чем сегодняшняя дата. И не может быть мнньше, чем 1 Марта 2021!');
            return view('report.wrong');
        }

        $tables = $this->create_table($r);
        $data['data'][] = $tables[0];
        $data['names'] = $this->names;

        return view('report.index', $data);

    }

    /* НОВЫЙ ОТЧЕТ -------------------------------------*/
    public function create_table(Request $r)
    {
        $sort = $r->get('sort');
        $date = $r->get('D');

        $data = DistributorReportController::getData($date);   // вывод из базы
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
        $summ_by_period['month'] = $this->totalByPeriod(Carbon::parse($data[0][4]), 'month');
        $summ_by_period['season'] = $this->totalByPeriod(Carbon::parse($data[0][4]), 'season');
        $summ_by_period['year'] = $this->totalByPeriod(Carbon::parse($data[0][4]), 'year');
        $j = 0;

        for ($i = 0; $i < count($data); $i++) {

            if (in_array($data[$i][1], $this->do_not_show)) continue;

            $last_key = count($data[$i]) - 1;
            $firstDate = Carbon::parse($data[0][4]);
            $firstDateW = $firstDate;
            $name_row = preg_match($pattern, $data[$i][1]);
            $second_magaz = '';
            $start_row = '';
            $name = trim($data[$i][1]);

            if ($i == 0) { // если шапка таблицы
                $t .= '<thead class="table-head"><tr><td class="places-cell"></td>';
            } else {
                if ($i == 1) {
                    $t .= '<tbody>';
                }
                if ($name_row && $data[$i][1] != 'Офис Харьков 120' and $data[$i][1] != "СайтС" and $data[$i][3] != null and $data[$i][0] != 'УВОЛЕН') { // если ряд с именем

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
//                    else if ($key != $last_key) $day = $ruDaysArr[Carbon::parse($cell)->format('w')]; // дни недели. на русском
                }


                /*  Расскраска понедельно и  "Итого за неделю" ------*/

//                if ($key > 3 and $key != $last_key) {
//
//                    if (Carbon::parse($firstDate)->format('N') == 7 and isset($start_row)) {   //если Воскресенье - последний день в таблице
//                        unset($start_row);
//                        $firstDateW = Carbon::parse('monday'); //просто заглушка
//                    }
//                    $weekcolor = ((int)Carbon::parse($data[0][$key])->format('W') % 2) ? 'notgrey' : 'grey';  // класс для разделения ПОНЕДЕЛЬНО
//                    if ($firstDateW->format('W') != Carbon::parse($data[0][$key])->format('W')) {
//                        $firstDateW = Carbon::parse($data[0][$key]);
//                        $totalW = $this->totalByPeriod($data[$i][1], Carbon::parse($data[0][$key]), 'week');
//                        $trends_arr[] = $totalW;
//                        $t .= ($i == 0) ? '<td class="sort week-bg date ' . $weekcolor . '"> <div class="date-wrap week-head no-events">Неделя</div>   </td>' :
//                            '<td class="week-bg ' . $weekcolor . '">' . $this->bigAndSmall($totalW) . '</td>';
//                    }
//                }
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
                        if ($firstDateW->format('W') != Carbon::parse($data[0][$key])->format('W')) {
                            $firstDateW = Carbon::parse($data[0][$key]);
                            $totalW = (isset($summ_by_period[$firstDateW->format('W')][trim($data[$i][1])])) ? $summ_by_period[$firstDateW->format('W')][trim($data[$i][1])] : 0;
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
                        if (!$summ_of_two) {
                            $t .= '<td>' . $this->bigAndSmall($cell) . '</td>';
                        }

                    } else {
                        $t .= '<td class="' . $weekcolor . '">' . $this->bigAndSmall($cell) . '</td>';
                    }
                }
            }

            /*------------------------------- СТАТИСТИКА МЕСЯЦ, СЕЗОН, ГОД. ШАПКА и ТЕЛО  --*/
            if ($i == 0) {
                $t .= '<td class="current-month-head date sort total-data-border"><div class="date-wrap no-events">' . $ruMonths[Carbon::parse($firstDate)->subMonth()->format('n')] .
                    '</div></td>';
                $t .= '<td class="current-season-head date sort"><div class="date-wrap no-events">Сезон</div></td>';
                $t .= '<td class="current-year-head date sort"><div class="date-wrap no-events">Год</div></td>';

                $t .= "</tr></thead>";
            } else {
                if (count($weeks_nums) != 4 or
                    !isset($summ_by_period[$weeks_nums[2]][$name]) or
                    !isset($summ_by_period[$weeks_nums[3]][$name]) or
                    ($summ_by_period[$weeks_nums[2]][$name] + $summ_by_period[$weeks_nums[3]][$name]) == 0) $trend = '';  //ТРЕНДЫ
                else {
                    $weeek_1 = (isset($summ_by_period[$weeks_nums[0]][$name])) ? $summ_by_period[$weeks_nums[0]][$name] : 0;
                    $weeek_2 = (isset($summ_by_period[$weeks_nums[1]][$name])) ? $summ_by_period[$weeks_nums[1]][$name] : 0;
                    $weeek_3 = (isset($summ_by_period[$weeks_nums[2]][$name])) ? $summ_by_period[$weeks_nums[2]][$name] : 0;
                    $weeek_4 = (isset($summ_by_period[$weeks_nums[3]][$name])) ? $summ_by_period[$weeks_nums[3]][$name] : 0;
                    $trend = round(($weeek_1 + $weeek_2) / ($weeek_3 + $weeek_4) * 100) . '&#176;';
                }
                $t = str_replace('trendrow' . $i, $trend, $t);

                $t .= '<td class="current-month-summ total-data-border">';
                $t .= (isset($summ_by_period['month'][$name])) ?  $this->bigAndSmall($summ_by_period['month'][$name]) : 0;
                $t .= '</td>';

                $t .= '<td class="current-month-summ total-data-border">';
                $t .= (isset($summ_by_period['season'][$name])) ?  $this->bigAndSmall($summ_by_period['season'][$name]) : 0;
                $t .= '</td>';

                $t .= '<td class="current-month-summ total-data-border">';
                $t .= (isset($summ_by_period['year'][$name])) ?  $this->bigAndSmall($summ_by_period['year'][$name]) : 0;
                $t .= '</td>';

                $t .= '</tr>';
            }
        }
        $t .= '</tbody></table>';
        return array($t);
    }

    /*   Массив ключей для сортировки    */
    public function getKeysForSort($data, $key)
    {
        $arr = array();

        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i][$key] != 'null' and $data[$i][$key] != null)
                $arr[] = $data[$i][$key];

        }
        ($key == 3) ? rsort($arr) : sort($arr);

        return $arr;
    }

    /*  Сортировка по Среднестатистическому (Скользящему)*/
    public function shuffle_slice($keys, $data, $new_data)
    {
        $this->keys = $keys;
        $this->shuffleData = $data;
        $this->shuffled = $new_data;

        if (count($this->keys) > 0) {
            for ($i = 1; $i < count($this->shuffleData); $i++) {
                if (isset($this->keys[0])) {
                    if ($this->shuffleData[$i][3] == $this->keys[0]) {
                        $j = $i;
                        $this->shuffled[] = $this->shuffleData[$j];
                        while (isset($this->shuffleData[$j + 1]) and $this->shuffleData[$j + 1][3] == 'null') {
                            $this->shuffled[] = $this->shuffleData[$j + 1];
                            $j++;
                        }
                        array_shift($this->keys);
                    }
                }
            }
            self::shuffle_slice($this->keys, $this->shuffleData, $this->shuffled);
        }

        return $this->shuffled;
    }

    /*  Сортировка по номеру магазина*/
    public function shuffle_store($keys, $data, $new_data)
    {
        $this->keys = $keys;
        $this->shuffleData = $data;
        $this->shuffled = $new_data;

        if (count($this->keys) > 0) {
            for ($i = 1; $i < count($this->shuffleData); $i++) {
                if (isset($this->keys[0])) {
                    if ($this->shuffleData[$i][0] == $this->keys[0]) {
                        $this->shuffled[] = $this->shuffleData[$i];
                        $j = $i;
                        while (isset($this->shuffleData[$j + 1]) and $this->shuffleData[$j + 1][0] == null) {
                            $this->shuffled[] = $this->shuffleData[$j + 1];
                            $j++;
                        }
                        array_shift($this->keys);
                        $this->shuffleData[$i][0] = '---';
                    }
                }
            }
            self::shuffle_store($this->keys, $this->shuffleData, $this->shuffled);
        }

        return $this->shuffled;
    }

    /* Добавление Среднестатискического в общий массив*/    //И ТРЕНДОВ
    public function addMiddle($data)
    {
        $pattern = '/^[а-я]+/ui';

        for ($i = 1; $i < count($data); $i++) {
            $arr = array();
            $divider = count($data[$i]) - 3; // 1. номер магазина, 2. фио, 3. "Итого"

            if (preg_match($pattern, $data[$i][1]) && $data[$i][1] != 'Офис Харьков 120' and $data[$i][1] != "СайтС") {
                foreach (array_slice($data[$i], 2, $divider) as $q) {
                    if ($q != 'null') $arr[] = $q;
                }
                array_splice($data[$i], 2, 0, 'ТРЕНД');   // ТРЕНД
                array_splice($data[$i], 3, 0, ceil(array_sum($arr) / $divider));
            } else {
                array_splice($data[$i], 2, 0, 'null');
                array_splice($data[$i], 3, 0, 'null');
            }

        }

        return $data;
    }

    /* Добавление Магазина в общий массив*/
    public function addStore($data)
    {
        $pattern = '/^[а-я]+/ui';
        $date = Carbon::now()->format('Y-m-d');
        $db_data = DistributorsData::where('date', $date)->get()->toArray();
        foreach ($db_data as $d) {
            $keys[$d['name']] = $d['main_store'];
        }
        for ($i = 1; $i < count($data); $i++) {
            if (preg_match($pattern, $data[$i][1]) and $data[$i][1] != "Офис Харьков 120" and $data[$i][1] != "СайтС") {
                if (isset($keys[$data[$i][1]])) $data[$i][0] = strtok($keys[$data[$i][1]], ',');
                else $data[$i][0] = 'УВОЛЕН';
            }
        }
        return $data;
    }

    // Обрезает имена в соответствии ТЗ. так-же создает оссоциативный массив типа "короткое имя" => "полное имя". Маcсив спользуется в js
    public function trimName($data)
    {
        $name = $data[1];
        $store = $data[0];

        $arr = explode(' ', $name);

        if (count($arr) == 1) return $arr[0];

        if ($arr[1][0] == '(' and isset($arr[2])) $shortName = mb_substr($arr[0], 0, 5) . mb_substr($arr[2], 0, 1);
        else $shortName = mb_substr($arr[0], 0, 5) . mb_substr($arr[1], 0, 1);

        $this->names[$shortName][] = $name;
        $this->names[$shortName][] = $store;
        return $shortName;
    }

    public function trimDate($date)
    {
        $ruDaysArr = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        return Carbon::parse($date)->format('d.m') . '&nbsp;' . $ruDaysArr[Carbon::parse($date)->format('w')];
    }

    public function totalByPeriod(Carbon $date, $period)
    {
        $arr = array();

        switch ($period) {
            case 'week':
                $end = $date->endOfWeek()->format('Y-m-d');
                $start = Carbon::parse($end)->subDays(6)->format('Y-m-d');
                break;
            case 'month':
                $end = $date->subMonth()->endOfMonth()->format('Y-m-d');
                $start = $date->startOfMonth()->format('Y-m-d');
                break;
            case 'season':
                if (Carbon::parse($date)->format('n') > 2 and 9) {
                    $start = Carbon::parse($date)->format('Y') . '-03-01';
                    $end = Carbon::parse($date)->format('Y') . '-08-31';
                } elseif (Carbon::parse($date)->format('n') == 1 or Carbon::parse($date)->format('n') == 2) {

                    $start = Carbon::parse($date)->subYear()->format('Y') . '-09-01';
                    $end = Carbon::parse($date)->format('Y') . '-02-28';
                } else {
                    $start = Carbon::parse($date)->format('Y') . '-09-01';
                    $end = Carbon::parse($date)->addYear()->format('Y') . '-02-28';
                }
                break;
            case 'year':
                if (Carbon::parse($date)->format('n') == 1 or Carbon::parse($date)->format('n') == 2) {
                    $start = Carbon::parse($date)->subYear()->format('Y') . '-03-01';
                    $end = Carbon::parse($date)->format('Y') . '-02-28';
                } else {
                    $start = Carbon::parse($date)->format('Y') . '-03-01';
                    $end = Carbon::parse($date)->addYear()->format('Y') . '-02-28';
                }
                break;
            default:
                $end = $date->format('Y-m-d');
                $start = $date->format('Y-m-d');

        }

        $data = DB::table('distributors')->select(DB::raw('name, sum(summ) as total'))->whereBetween('date', array($start, $end))->groupBy('name')->get();
        foreach ($data as $dist) {
            $arr[trim($dist->name)] = $dist->total;
        }
        return $arr;

    }

    /* Форматирование выручки для нового отчета*/
    public function bigAndSmall($str)
    {
        if (is_null($str)) return $str;
        $str = (int)$str;
        $str = number_format($str, 0, '', ',');
        $arr = explode(',', $str);

        if (count($arr) > 2) {
            if ($arr[1][1] < 5) return '<span class="hidden-million">m</span><span class="big">' . $arr[0] . '<small class="small">' . $arr[1][0] . '</small>\'</span>';
            $r = $arr[1][0] + 1;
            return '<span class="hidden-million">m</span><span class="big">' . $arr[0] . '<small class="small">' . $r . '</small>\'</span>';
        }
        if (count($arr) == 1) {
            if (!isset($arr[0][1])) return '<span class="big"><small class="small">' . $arr[0][0] . '</small></span>';
            if ($arr[0] == '') return $arr[0];
            if ($arr[0][0] == '-') {
                if (strlen($arr[0]) == 4) return '<span class="big"><small class="small">' . $arr[0][0] . $arr[0][1] . '</small></span>';
                else return '<span class="big"><small class="small">' . $arr[0][0] . '0' . $arr[0][1] . '</small></span>';
            }
            if (strlen($arr[0]) == 2) return '<span class="big"><small class="small">' . '0' . $arr[0][0] . '</small></span>';

            $s = ($arr[0][1] < 5) ? $arr[0][0] : (int )$arr[0][0] + 1;
            if ($s == 10) return '<span class="big">' . ($s - 9) . '<small class="small">' . 0 . '</small></span>';
            return '<span class="big"><small class="small">' . $s . '</small></span>';

        }
        if (strlen($arr[0]) > 1) {
            if ($arr[1][0] < 5) {
                return '<span class="big">' . $arr[0] . '</span>';
            } else {
                $r = $arr[0] + 1;
                return '<span class="big">' . $r . '</span>';
            }
        }
        $s = ($arr[1][1] < 5) ? $arr[1][0] : (int )$arr[1][0] + 1;
        if ($s == 10) {
            $s = 0;
            $arr[0] += 1;
            if ($arr[0] == 10) return '<span class="big">' . $arr[0] . '</span>';
        }
        return '<span class="big">' . $arr[0] . '<small class="small">' . $s . '</small></span>';
    }
}


//// Перевернуть, если даты в обратном порядке
//public function reverse($data)
//{
//    $reverse = array();
//
//    for ($i = 0; $i <= 7; $i++) {
//        $reverse[] = $data[$i];
//    }
//    for ($i = 8; $i < count($data) - 1; $i++) {
//        for ($j = 0; $j < 2; $j++) {
//            $reverse[$i][] = $data[$i][$j];
//        }
//    }
//    for ($i = 8; $i < count($data) - 1; $i++) {
//        for ($j = count($data[$i]) - 2; $j > 1; $j--) {
//            $reverse[$i][] = $data[$i][$j];
//        }
//    }
//    for ($i = 8; $i < count($data) - 1; $i++) {
//        $reverse[$i][] = $data[$i][count($data[$i]) - 1];
//    }
//    $reverse[] = $data[count($data) - 1];
//
//    return $reverse;
//}
//
//// Очистка от ненужных строк до города
//public function trimData($data)
//{
//    $cities = $this->cities;
//
//    for ($i = 1; $i < count($data); $i++) {
//        if (in_array($data[1][1], $cities)) break;
//        array_splice($data, 1, 1);
//    }
//    array_pop($data); // Удалить Итого
//
//    return $data;
//}
//
//public function trimCity($data, $city)
//{
//    $arr[] = $data[0];
//    $cities = $this->cities;
//
//    for ($i = 1; $i < count($data); $i++) {
//        if ($data[$i][1] == $city) {
//            $j = $i + 1;
//            while ($j < count($data) and !in_array($data[$j][1], $cities) and $data[$j][1] != 'Итог') {
//                $arr[] = $data[$j];
//                $j++;
//            }
//        }
//    }
//    array_splice($arr[0], 2, 0, 'Скол'); // место для "скользящее"
//
//    return $arr;
//}
