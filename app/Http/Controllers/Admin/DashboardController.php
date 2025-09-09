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

        return response()->json([
            'batch' =>$batchCount,
            'enrolledstudents' =>$enrolledstudentCount,
            'finishedexams' => $finishedExamsCount,
        ]
    );
    }
}
