<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sstosai18t extends Model
{
    protected $table = 'sstosai18t';

    protected $fillable = [
        'sai_a',
        'month_a',
        'sai_b',
        'month_b',
        'sai_c',
        'month_c',
        'sai_d',
        'month_d',
    ];
}
