<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();


        $password = strtolower($validated['email']);

        $validated['password'] = $password;

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);


        return response()->json([
            'user' => $user,
        ], 201);
    }


        public function show(User $user)
    {
        return response()->json($user,200);
    }

        public function destroy(User $user)
    {
        $user = $user->delete();
         return response()->json([
            'message' => 'User deleted successfully',
            'data' => $user
        ], 200);

    }
}
