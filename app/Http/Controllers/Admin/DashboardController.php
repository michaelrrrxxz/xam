<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;

class DashboardController extends Controller
{
    public function index(){
        $batchCount = Batch::count();

        return response()->json(['batch' =>$batchCount]);
    }
}
