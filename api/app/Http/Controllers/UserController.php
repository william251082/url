<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;

class UserController extends Controller
{
    public function showUsers(): JsonResponse
    {
        return response()->json(['users' =>  User::all()], 200);
    }

    public function singleUser($id)
    {
       try {
           $user = User::findOrFail($id);
           return response()->json(['user' => $user], 200);
       } catch (\Exception $e) {
           return response()->json(['message' => 'user not found!'], 404);
       }
    }

    public function profile(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
