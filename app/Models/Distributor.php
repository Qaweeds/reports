<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Matrix\Builder;


class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'summ',
        'store'
    ];

}
