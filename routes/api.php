<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\EnrolledStudentController;



use App\Http\Controllers\Examinee\VerifyController as ExamineeVerifyController;
use App\Http\Controllers\Examinee\InformationController as ExamineeInformationController;
use App\Http\Controllers\Examinee\SchoolController as ExamineeSchoolController;
use App\Http\Controllers\Examinee\TrialController as ExamineeTrialController ;
use App\Http\Controllers\Examinee\QuestionController as ExamineeQuestionController;
use App\Http\Controllers\Examinee\ResultController as ExamineeResultController;

 use App\Http\Controllers\Admin\ResultController as AdminResultController;
 use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use  App\Http\Controllers\Admin\ImportEnrolledStudentController;
 use App\Http\Controllers\Admin\DashboardController;
Route::middleware(['auth:sanctum'])->get('/v1/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(function () {

    Route::prefix('admin')->group(function(){
        Route::apiResource('result', AdminResultController::class)->only(['index']);
        Route::get('/results/batch/{id}', [AdminResultController::class, 'resultsByBatch']);
        Route::apiResource('dashboard', DashboardController::class)->only(['index']);
        Route::apiResource('exam', ExamController::class);
        Route::apiResource('batch', BatchController::class);
        Route::apiResource('question', AdminQuestionController::class);
        Route::apiResource('enrolledStudent', EnrolledStudentController::class);
        Route::post('enrolled-students/upload',[ImportEnrolledStudentController::class, 'store']);
        Route::put('batch/{id}/lock', [BatchController::class, 'lock']);
        Route::put('batch/{id}/activate', [BatchController::class, 'activate']);
    });


    Route::prefix('examinee')->group(function(){
        Route::apiResource('verify', ExamineeVerifyController::class)->only(['store']);
        Route::apiResource('information', ExamineeInformationController::class)->only(['store']);
        Route::apiResource('school', ExamineeSchoolController::class)->only(['index']);
        Route::apiResource('trial', ExamineeTrialController::class)->only(['index']);
        Route::apiResource('question', ExamineeQuestionController::class)->only(['index']);
        Route::apiResource('result', ExamineeResultController::class)->only(['store']);
        Route::get('result/{student}', [ExamineeResultController::class, 'show']);
    });

    // 🔓 Public routes








    // 🔒 Protected routes
    // Route::middleware('auth:sanctum')->group(function () {

    // });
});



require __DIR__.'/auth.php';
