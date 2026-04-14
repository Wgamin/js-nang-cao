<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. Kiểm tra Email
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email này không tồn tại trong hệ thống.'],
            ]);
        }

        // 2. Kiểm tra Mật khẩu
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Mật khẩu không chính xác.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Logout user (Revoke token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        // Nạp thêm thông tin profile tùy theo role
        $profile = null;
        if ($user->role === 'teacher') {
            $profile = $user->teacher;
        } elseif ($user->role === 'parent') {
            $profile = $user->guardian;
        } elseif ($user->role === 'student') {
            $profile = $user->student;
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile
        ]);
    }
}
