<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => $request->user(),
                'token' => $request->user()->createToken('auth-token')->plainTextToken,
            ]);
        } 
        
        return response()->json([
            'message' => 'Las credenciales proporcionadas son incorrectas.',
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}
