<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Http\Requests\VerifyRequest;
use App\Models\EnrolledStudent;
use App\Models\Batch;
use App\Models\Result;
class VerifyController extends Controller
{
public function store(VerifyRequest $request)
{
    $validated = $request->validated();

    // Find student
    $student = EnrolledStudent::where('id_number', $validated['id_number'])->first();
    if (!$student) {
        return response()->json([
            'message' => 'Student not found.',
        ], 404);
    }

    // Get active batch
    $activeBatch = Batch::where('isLocked', false)->first();
    if (!$activeBatch) {
        return response()->json([
            'message' => 'No active batch found.',
        ], 404);
    }

    // Validate access key
    $submittedKey = strtolower(trim($validated['access_key']));
    $storedKey    = strtolower(trim($activeBatch->access_key));

    if ($submittedKey !== $storedKey) {
        return response()->json([
            'message' => 'Invalid Access Key. Please try again.',
        ], 403);
    }

    // ✅ Check attempts from results table
    $examAttempts = Result::where('student_id', $student->id)
        ->where('batch_id', $activeBatch->id)
        ->count();

    if ($examAttempts >= 1) {
        // If admin credentials are provided → validate them
        if (!empty($request->admin_email) && !empty($request->admin_password)) {
            $admin = \App\Models\User::where('email', $request->admin_email)->first();

            if (!$admin || !\Hash::check($request->admin_password, $admin->password)) {
                return response()->json([
                    'message' => 'Invalid admin credentials.',
                ], 401);
            }

            // ✅ Admin validated → allow student to continue
        } else {
            // No admin credentials → reject
            return response()->json([
                'message' => 'Student already took the exam. Admin credentials required to continue.',
            ], 403);
        }
    }

    return response()->json([
        'message' => 'Verification successful',
        'student' => [
            'id'          => $student->id,
            'id_number'   => $student->id_number,
            'first_name'  => $student->first_name,
            'last_name'   => $student->last_name,
            'middle_name' => $student->middle_name,
            'course'      => $student->course,
            'birth_day'   => $student->birth_day,
            'gender'      => $student->gender,
        ],
        'batch' => [
            'id'     => $activeBatch->id,
            'name'   => $activeBatch->name,
            'status' => $activeBatch->status,
        ],
    ], 200);
}



}
