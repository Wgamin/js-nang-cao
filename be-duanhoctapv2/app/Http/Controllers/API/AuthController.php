<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Đăng nhập và trả về Token Sanctum + Role.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không đúng.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $user->createToken('API Token')->plainTextToken,
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

    /**
     * Đăng xuất (Revoke token hiện tại).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'data' => 'Đăng xuất thành công!'
        ]);
    }

    /**
     * Lấy thông tin user đang đăng nhập.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }
}
