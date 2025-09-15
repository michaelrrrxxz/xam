<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'answer',
    ];

    protected $casts = [
        'answer' => 'array',
    ];


    // Relationship to student
    public function student()
    {
        return $this->belongsTo(EnrolledStudent::class, 'student_id');
    }



}
