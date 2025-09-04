<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
      protected $fillable = [
        'format',
        'test_type',
        'question',
        'answer',
        'ch1',
        'ch2',
        'ch3',
        'ch4',
        'ch5',
        'qtype',
    ];
}
