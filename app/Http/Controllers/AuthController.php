<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function validateInput($request)
    {
        return $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::guard('api')->attempt($this->validateInput($request))) {
            $request->session()->regenerate();

            return response()->json([], 204);
        }

        return response()->json([
            'error' => 'Invalid credentials',
        ], 401);
    }

    public function destory(Request $request)
    {
        Auth::guard('api')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'successfully logout',
        ]);
    }
}
