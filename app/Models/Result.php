<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
        protected $fillable = [
        'batch_id',
        'student_id',
        'total_score',
        'verbal',
        'non_verbal',
        'verbal_reasoning',
        'verbal_comprehension',
        'quantitative_reasoning',
        'figural_reasoning',
        
          'total_performance_category',
            'verbal_performance_category' ,
            'verbal_comprehension_category',
            'verbal_reasoning_category',
            'non_verbal_performance_category',
            'figural_reasoning_category',
            'quantitative_reasoning_category',
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
