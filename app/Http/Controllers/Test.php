<?php

namespace App\Http\Controllers;


use PhpOffice\PhpSpreadsheet\IOFactory;

class Test extends Controller
{
    public function __invoke()
    {
        $excel = IOFactory::load(base_path('storage/app/public/dist/prices.xlsx'));
        $data = $excel->getActiveSheet()->toArray(null, true, false);
        $head_ignore = array(1, 2, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 21, 22, 25, 26, 29, 30, 33, 34, 37, 38);
        $all_ignore = array(3, 7, 11, 15, 19, 23, 27, 31, 35, 39);
        $triple_row = array(0, 20, 24, 28, 32, 36);
        $bold_values = array(0, 4, 8, 12, 16, 20, 24, 28, 32, 36);

        $table = '<table>';
        foreach ($data as $key => $value) {
            $table .= '<tr>';
            foreach ($value as $k => $v) {
                if (in_array($k, $all_ignore)) continue;
                if ($key == 0) {
                    if (in_array($k, $head_ignore)) continue;
                    elseif (in_array($k, $triple_row)) $table .= '<td class="head-border" colspan="3"><b>' . $v . '</b></td>';
                    elseif ($k == 4) $table .= '<td class="head-border"><small>' . $v . '</small></td>';
                    elseif ($k == 5) $table .= '<td class="head-border" colspan="2"><b>' . $v . '</b></td>';
                    elseif ($k == 8) $table .= '<td class="head-border" colspan="9"><b>' . $v . '</b></td>';
                } else {
                    if (in_array($k + 1, $all_ignore)) $table .= '<td class="border-r">' . $v . '</td>';
                    elseif (in_array($k, $bold_values)) $table .= '<td class="border-r"><b>' .'<span class="code">'.  $v.'</span>' . ':</b></td>';
                    else $table .= '<td>' . $v . '</td>';

                }
            }
            $table .= '</tr>';
        }
        $table .= '</table>';

        return view('prices.index', compact('table'));
    }
}
