<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolledStudent extends Model
{
    /** @use HasFactory<\Database\Factories\EnrolledStudentFactory> */
    use HasFactory;
     protected $table = 'enrolled_students';
    protected $fillable = [
        'id_number',
        'last_name',
        'first_name',
        'middle_name',
        'birth_day',
        'course',
        'gender'
];


public function information()
{
    return $this->hasOne(Information::class, 'enrolledstudent_id');
}
}


