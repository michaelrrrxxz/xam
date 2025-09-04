<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;

use App\Http\Requests\InformationRequest;
use Illuminate\Http\Request;
use App\Models\Information;
use App\Models\School;

class InformationController extends Controller
{
public function store(InformationRequest $request)
{

    $validated = $request->validated();


    School::firstOrCreate([
        'name' => $validated['school']
    ]);


    $information = Information::create($validated);

    return response()->json([
        'message' => 'Information saved successfully',
        'data' => $information
    ], 201);
}


}
