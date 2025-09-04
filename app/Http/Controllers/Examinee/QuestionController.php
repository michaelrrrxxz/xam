<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;
use App\Models\Question;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $question = Question::get(['id','question', 'ch1','ch2', 'ch3', 'ch4', 'ch5']);
        return response()->json($question,200);
    }

}
