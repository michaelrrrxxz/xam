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
}
