<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scaledscore extends Model
{
        protected $fillable = [
    'raw_score_t' ,
    'scaled_score_t' ,
    'raw_score_v' ,
    'scaled_score_v' ,
    'raw_score_nv' ,
    'scaled_score_nv',
    ];
}
