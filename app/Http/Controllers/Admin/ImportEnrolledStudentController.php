<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportEnrolledStudentRequest;
use App\Imports\EnrolledStudentsImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ImportEnrolledStudentController extends Controller
{
    public function store(ImportEnrolledStudentRequest $request)
    {
        $validated = $request->validated();


        if ($request->hasFile('file')) {
            $file = $request->file('file');

            Excel::import(new EnrolledStudentsImport, $file);


        }

        return response()->json([
            'message' => 'Imported successfully'], 201);
    }
}
