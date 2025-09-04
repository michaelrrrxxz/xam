<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;

use App\Models\Trial;
use Illuminate\Http\Request;

class TrialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trials = Trial::all();

        return response()->json($trials);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trial $trial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trial $trial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trial $trial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trial $trial)
    {
        //
    }
}
