<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showUsers(): JsonResponse
    {
        return response()->json(User::all());
    }

    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }
}
