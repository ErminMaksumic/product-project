<?php


namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {
//        dd($request['role_id']);
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role_id' => 1,
        ]);
        return response()->json([
            'message' => 'User Created ',
        ]);
    }
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            // successfull authentication
            $user = User::find(Auth::user()->id);

            $user_token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $user_token,
                'user' => $user,
            ], 200);
        } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }

    public function logout(Request $request): string
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
