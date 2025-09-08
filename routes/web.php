<?php

use Illuminate\Support\Facades\Route;

 use App\Http\Controllers\Admin\ResultController;
 use App\Http\Controllers\BatchController;


Route::get('/results/{id}/print', [ResultController::class, 'print'])->name('results.print');
Route::get('batch/results/{id}/print', [BatchController::class, 'print'])->name('batch.print');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any','.*');




