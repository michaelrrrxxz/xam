<?php

namespace App\Http\Controllers;

use App\Models\EnrolledStudent;
use App\Http\Requests\StoreEnrolledStudentRequest;
use App\Http\Requests\UpdateEnrolledStudentRequest;

class EnrolledStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrolledStudent = EnrolledStudent::get();
        return response()->json($enrolledStudent,200);
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
    public function store(StoreEnrolledStudentRequest $request)
    {
        $validated = $request->validated();
        $enrolledStudent = EnrolledStudent::create($validated);
        return response()->json([
            'message' => 'EnrolledStudent created successfully',
            'data' => $enrolledStudent
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EnrolledStudent $enrolledStudent)
    {
        return response()->json($enrolledStudent,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EnrolledStudent $enrolledStudent)
    {
        return response()->json($enrolledStudent,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrolledStudentRequest $request, EnrolledStudent $enrolledStudent)
    {
        $validated = $request->validated();

        $enrolledStudent->update($validated);

        return response()->json([
            'message' => 'EnrolledStudent created successfully',
            'data' => $enrolledStudent
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EnrolledStudent $enrolledStudent)
    {
        $enrolledStudent = $enrolledStudent->delete();
         return response()->json([
            'message' => 'EnrolledStudent deleted successfully',
            'data' => $enrolledStudent
        ], 200);

    }
}
