<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorTimesheet extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'store',
        'date',
        'work',
        'name'
    ];
}
