<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register Berhasil',
            'token' => $token,
            'user' => $user
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Login Gagal, Email/Password tidak valid'], status: Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Login Berhasil',
            'token' => $token,
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'message' => 'Berhasil get user',
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Logout Berhasil'], Response::HTTP_OK);
    }

}
