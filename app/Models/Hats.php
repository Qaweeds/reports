<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hats extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'store',
        'date',
        'cashbox',
        'cashbox_ret',
        'income',
        'profit',
        'income_piece',
        'rent',
        'salary',
        'other_costs',
        'distributed_costs',
        'discounts',
        'items_sold',
        'items_sold_ret',
        'items_returned',
        'unique_sku',
        'area',
        'dollar_rate',
        'SUPERINCOME',

    ];

}
