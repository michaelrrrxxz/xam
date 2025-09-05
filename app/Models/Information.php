<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $fillable =
    [
    'enrolledstudent_id',
    'address',
    'school',
    'group_abc',
];
}
