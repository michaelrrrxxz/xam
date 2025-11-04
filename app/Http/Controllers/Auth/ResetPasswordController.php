<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    public function __invoke(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $tempPassword = Str::random(12);

        $user->password = Hash::make($tempPassword);
        $user->save();

        Log::info('Password reset', [
            'admin_id' => $request->user()->id,
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'temporary_password' => $tempPassword,
        ]);
    }
}
