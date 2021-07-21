<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use App\Models\DistributorsData;
use Carbon\Carbon;
use App\Models\Distributor;
use App\Models\DistributorsRetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class BaseDistibutorsController extends Controller
{

    private $keys;
    private $shuffleData;
    private $shuffled;
    protected $names;
    protected $cities = ['Харьков', 'Одесса', 'Хмельницкий'];
    public static $distributor;
    protected static $table;

    public function __construct()
    {
        if (Request::path() == 'all') {
            self::$distributor = new DistributorsRetail();
            self::$table = 'distributors_retails';
        } else {
            self::$distributor = new Distributor();
            self::$table = 'distributors';
        }
    }

    // сумма за промжуток
    public function totalByPeriod(Carbon $date, $period)
    {
        $arr = array();
        $date = $date->copy();

        switch ($period) {
            case 'week':
                $end = $date->endOfWeek()->format('Y-m-d');
                $start = $date->subDays(6)->format('Y-m-d');
                break;
            case 'month':
                $end = $date->subMonth()->endOfMonth()->format('Y-m-d');
                $start = $date->startOfMonth()->format('Y-m-d');
                break;
            case 'season':
                if ($date->format('n') > 2 and 9) {
                    $start = $date->format('Y') . '-03-01';
                    $end = $date->format('Y') . '-08-31';
                } elseif ($date->format('n') == 1 or $date->format('n') == 2) {

                    $start = $date->subYear()->format('Y') . '-09-01';
                    $end = $date->format('Y') . '-02-28';
                } else {
                    $start = $date->format('Y') . '-09-01';
                    $end = $date->addYear()->format('Y') . '-02-28';
                }
                break;
            case 'year':
                if ($date->format('n') == 1 or $date->format('n') == 2) {
                    $start = $date->subYear()->format('Y') . '-03-01';
                    $end = $date->format('Y') . '-02-28';
                } else {
                    $start = $date->format('Y') . '-03-01';
                    $end = $date->addYear()->format('Y') . '-02-28';
                }
                break;
            default:
                $end = $date->format('Y-m-d');
                $start = $date->format('Y-m-d');

        }

        $data = DB::table(self::$table)->select('name', DB::raw('sum(summ) as total'))->whereBetween('date', array($start, $end))->groupBy('name')->get();
        foreach ($data as $dist) {
            $arr[trim($dist->name)] = $dist->total;
        }
        return $arr;
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

    // Перевернуть, если даты в обратном порядке
    public function reverse($data)
    {
        $reverse = array();

        for ($i = 0; $i <= 7; $i++) {
            $reverse[] = $data[$i];
        }
        for ($i = 8; $i < count($data) - 1; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $reverse[$i][] = $data[$i][$j];
            }
        }
        for ($i = 8; $i < count($data) - 1; $i++) {
            for ($j = count($data[$i]) - 2; $j > 1; $j--) {
                $reverse[$i][] = $data[$i][$j];
            }
        }
        for ($i = 8; $i < count($data) - 1; $i++) {
            $reverse[$i][] = $data[$i][count($data[$i]) - 1];
        }
        $reverse[] = $data[count($data) - 1];

        return $reverse;
    }

    // Очистка от ненужных строк до города
    public function trimData($data)
    {
        $cities = $this->cities;

        for ($i = 1; $i < count($data); $i++) {
            if (in_array($data[1][1], $cities)) break;
            array_splice($data, 1, 1);
        }
        array_pop($data); // Удалить Итого

        return $data;
    }

    public function trimCity($data, $city)
    {
        $arr[] = $data[0];
        $cities = $this->cities;

        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i][1] == $city) {
                $j = $i + 1;
                while ($j < count($data) and !in_array($data[$j][1], $cities) and $data[$j][1] != 'Итог') {
                    $arr[] = $data[$j];
                    $j++;
                }
            }
        }
        array_splice($arr[0], 2, 0, 'Скол'); // место для "скользящее"

        return $arr;
    }
}
