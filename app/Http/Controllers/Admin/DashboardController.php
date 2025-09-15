<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\EnrolledStudent;
use App\Models\Result;

class DashboardController extends Controller
{
    public function index(){
        $batchCount = Batch::count();
        $enrolledstudentCount = EnrolledStudent::count();
         $finishedExamsCount = Result::count();

         $recent = Result::with([
        'batch:id,name',
        'student:id,id_number,last_name,first_name,middle_name,birth_day,course,gender'
        ])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();



        return response()->json([
            'batch' =>$batchCount,
            'enrolledstudents' =>$enrolledstudentCount,
            'finishedexams' => $finishedExamsCount,
            'recent' => $recent,
        ]
    );
    }
}
