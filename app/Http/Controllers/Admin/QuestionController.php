<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

use App\Services\QuestionService;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::orderBy('id', 'asc')->get();
        return response()->json($questions, 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionRequest $request, QuestionService $service)
    {
        $validated = $request->validated();

        $question = $service->store($validated, $request);

        return response()->json([
            'message' => 'Question created successfully',
            'data'    => $question,
        ], 201);
    }

    public function show(Question $question, QuestionService $service)
    {
        return response()->json($service->transformUrls($question), 200);
    }





    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        return response()->json($question,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $validated = $request->validated();

        $question->update($validated);

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question = $question->delete();
         return response()->json([
            'message' => 'Question deleted successfully',
            'data' => $question
        ], 200);

    }
}

