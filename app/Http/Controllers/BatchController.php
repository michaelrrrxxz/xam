<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Result;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batch = Batch::get();
        return response()->json($batch,200);
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
    public function store(StoreBatchRequest $request)
    {

        $activeBatch = Batch::where('isLocked', 0)->first();

        if ($activeBatch) {
            return response()->json([
                'message' => 'Cannot create a new batch while there is an active batch.',
                'active_batch' => $activeBatch
            ], 422);
        }

        $validated = $request->validated();

        do {
            $accessKey = rand(10000, 99999);
        } while (Batch::where('access_key', $accessKey)->exists());

        $validated['access_key'] = $accessKey;
        $batch = Batch::create($validated);

        return response()->json([
            'message' => 'Batch created successfully',
            'data' => $batch
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        return response()->json($batch,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        return response()->json($batch,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $validated = $request->validated();

        $batch->update($validated);

        return response()->json([
            'message' => 'Batch created successfully',
            'data' => $batch
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        $batch = $batch->delete();
         return response()->json([
            'message' => 'Batch deleted successfully',
            'data' => $batch
        ], 200);

    }

    public function lock($id)
    {
        $batch = Batch::findOrFail($id);
        $batch->islocked = true;
        $batch->save();

        return response()->json([
            'message' => 'Batch locked successfully',
            'batch' => $batch
        ]);
    }
    public function activate($id)
        {

        $activeBatch = Batch::where('isLocked', 0)->first();

        if ($activeBatch) {
            return response()->json([
                'message' => 'An active batch already exists. Please lock it before activating another.',
                'active_batch' => $activeBatch
            ], 422);
        }

        $batch = Batch::findOrFail($id);
        $batch->isLocked = false;
        $batch->save();

        return response()->json([
            'message' => 'Batch activated successfully',
            'batch' => $batch
        ]);
    }


    public function print($batchId)
    {
        $results = Result::with([
                'batch:id,name',
                'student:id,id_number,last_name,first_name,middle_name,birth_day,course,gender'
            ])
            ->where('batch_id', $batchId)
            ->get();

        return view('results.batchprint', compact('results'));
    }


}
