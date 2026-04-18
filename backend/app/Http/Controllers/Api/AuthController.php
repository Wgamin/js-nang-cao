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
            'username' => 'required', // Login by username
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        // 1. Kiểm tra Username
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['Tên đăng nhập này không tồn tại trong hệ thống.'],
            ]);
        }

        // 2. Kiểm tra Trạng thái
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Tài khoản của bạn đã bị khóa.'],
            ]);
        }

        // 3. Kiểm tra Mật khẩu
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
                'username' => $user->username,
                'full_name' => $user->full_name,
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
            $profile = $user->teacher; // Quan hệ này cần defined trong model User
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile
        ]);
    }
}
