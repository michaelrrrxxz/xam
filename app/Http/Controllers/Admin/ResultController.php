<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
class ResultController extends Controller
{
  public function index()
    {
        $results = Result::with(['batch:id,name', 'student:id,id_number,last_name,first_name,middle_name,birth_day,course,gender'])
            ->get();

        return response()->json($results);
    }
}
