<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Записываем время входа
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            // Обновляем модель, чтобы включить новое значение
            $user->refresh();

            return response()->json([
                'message'        => 'Login successful',
                'user'           => $user,
                'last_login_at'  => $user->last_login_at,
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
