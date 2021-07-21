<?php

namespace App\Http\Controllers\Hats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseHatsController extends Controller
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
        'item_sold_ret_piece',
        'income_piece_percent_1',
    ];
}
