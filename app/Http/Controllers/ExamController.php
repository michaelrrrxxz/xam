<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exam = Exam::get();
        return response()->json($exam,200);
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
    public function store(StoreExamRequest $request)
    {
        $validated = $request->validated();
        $exam = Exam::create($validated);
        return response()->json([
            'message' => 'Exam created successfully',
            'data' => $exam
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        return response()->json($exam,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        return response()->json($exam,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $validated = $request->validated();

        $exam->update($validated);

        return response()->json([
            'message' => 'Exam created successfully',
            'data' => $exam
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam = $exam->delete();
         return response()->json([
            'message' => 'Exam deleted successfully',
            'data' => $exam
        ], 200);

    }
}
