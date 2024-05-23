<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'user.username' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:8',
        ]);

        // ユーザー情報の抽出
        $userDetails = $validatedData['user'];

        $user = User::create([
            'username' => $userDetails['username'],
            'email' => $userDetails['email'],
            'password' => Hash::make($userDetails['password']),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // トークンやユーザー情報を含むレスポンスを生成
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
