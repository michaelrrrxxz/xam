<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

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
public function store(StoreQuestionRequest $request)
{
    $validated = $request->validated();

    // Handle question
    if ($request->hasFile('question_image')) {
        $path = $request->file('question_image')->store('questions', 'public');
        $validated['question'] = $path; // still store the relative path in DB
    }

    // Handle choices
    foreach (['ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $choice) {
        if ($request->hasFile($choice . '_image')) {
            $path = $request->file($choice . '_image')->store('choices', 'public');
            $validated[$choice] = $path;
        }
    }

    $question = Question::create($validated);

    // Convert stored paths to full URLs for frontend
    $question->question = $question->question
        ? asset('storage/' . $question->question)
        : null;

    foreach (['ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $choice) {
        if ($question->$choice) {
            $question->$choice = asset('storage/' . $question->$choice);
        }
    }

    return response()->json([
        'message' => 'Question created successfully',
        'data'    => $question,
    ], 201);
}





    /**
     * Display the specified resource.
     */
public function show(Question $question)
{
    $baseUrl = asset('storage');

    foreach (['question', 'ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $field) {
        if ($question->$field && !str_starts_with($question->$field, 'http')) {
            $question->$field = $baseUrl . '/' . $question->$field;
        }
    }

    return response()->json($question, 200);
}


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

