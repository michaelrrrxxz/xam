<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

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
