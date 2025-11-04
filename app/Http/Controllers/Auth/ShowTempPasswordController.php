<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowTempPasswordController extends Controller
{
    public function show(Request $request, $id)
    {
        if (! $request->user() || ! $request->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $tempPassword = $request->session()->get('temp_password');
        $tempUser = $request->session()->get('temp_user');

        // ❌ if expired or invalid
        if (! $tempPassword || ! $tempUser || $tempUser['id'] != (int) $id) {
            return view('admin.users.temp_password_expired');
        }

        return view('admin.users.show_temp_password', [
            'password' => $tempPassword,
            'user' => $tempUser,
        ]);
    }
}
