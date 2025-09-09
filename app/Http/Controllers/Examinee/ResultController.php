<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\EnrolledStudent;
use App\Models\Question;
use App\Models\Batch;
use App\Models\Scaledscore;

use Carbon\carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Sstosai18t;

use App\Services\ResultService;

class ResultController extends Controller
{

     private $service;

    public function __construct(ResultService $service)
    {
        $this->service = $service;
    }

    public function show($studentId)
    {

          $result = Result::with(['batch:id,name', 'student:id,id_number,last_name,first_name,middle_name,birth_day,course,gender'])
          ->where('student_id', $studentId)
          ->latest()
          ->first();

        if (!$result) {
            return response()->json(['message' => 'No result found.'], 404);
        }

        return response()->json([
            'message' => 'Result fetched successfully',
            'result' => $result,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:enrolled_students,id',
            'school' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $batch = Batch::where('isLocked', false)->firstOrFail();

        $result = $this->service->storeResult($validated, $batch);

        return response()->json([
            'message' => 'Exam completed successfully',
            'result'  => $result
        ], 201);
    }
}




