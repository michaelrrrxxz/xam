<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
        protected $fillable = [
        'student_id',
        'batch_id',
        'test_ip',

        'total_score',
        'scaled_score_t',
        'sai_t',
        'pba_pr_t',
        'pba_s_t',
        'pbg_pr_t',
        'pbg_s_t',


        'verbal',
        'scaled_score_v',
        'sai_v',
        'pba_pr_v',
        'pba_s_v',
        'pbg_pr_v',
        'pbg_s_v',

        'non_verbal',
        'scaled_score_nv',
        'sai_nv',
        'pba_pr_nv',
        'pba_s_nv',
        'pbg_pr_nv',
        'pbg_s_nv',

        'verbal_comprehension',
        'verbal_comprehension_category',

        'verbal_reasoning',
        'verbal_reasoning_category',
        'figural_reasoning',
        'figural_reasoning_category',
        'quantitative_reasoning',
        'quantitative_reasoning_category',

        'total_performance_category',
        'verbal_performance_category' ,
        'non_verbal_performance_category',


    ];
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
    public function student()
    {
        return $this->belongsTo(EnrolledStudent::class, 'student_id');
    }
}
