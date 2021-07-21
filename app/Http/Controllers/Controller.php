<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
                if (strlen($arr[0]) == 4) return '<span class="big minus"><small class="small">' . $arr[0][0] . $arr[0][1] . '</small></span>';
                else return '<span class="big minus"><small class="small">' . $arr[0][0] . '0' . $arr[0][1] . '</small></span>';
            }
            if (strlen($arr[0]) == 2) return '<span class="big"><small class="small">' . '0' . $arr[0][0] . '</small></span>';

            $s = ($arr[0][1] < 5) ? $arr[0][0] : (int )$arr[0][0] + 1;
            if ($s == 10) return '<span class="big">' . ($s - 9) . '<small class="small">' . 0 . '</small></span>';
            return '<span class="big"><small class="small">' . $s . '</small></span>';

        }
        if (strlen($arr[0]) > 1) {
            if ($arr[0][0] == '-') {
                if ($arr[1][0] > 5) $r = $arr[1][0] + 1;
                else $r = $arr[1][0];
                return '<span class="big minus">' . $arr[0] . '<small class="small">'. $r . '</small></span>';
            } elseif ($arr[1][0] < 5) {
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
