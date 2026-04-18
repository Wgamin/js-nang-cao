<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'staff', 'teacher', 'user'])],
            'phone' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $user = User::create($validated);

        return response()->json([
            'message' => 'Người dùng đã được tạo thành công.',
            'user' => $user
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['sometimes', 'string', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'full_name' => 'sometimes|string',
            'password' => 'sometimes|nullable|string|min:6',
            'role' => ['sometimes', Rule::in(['admin', 'staff', 'teacher', 'user'])],
            'phone' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Cập nhật người dùng thành công.',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Không cho phép tự xóa chính mình
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Bạn không thể tự xóa tài khoản của chính mình.'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Người dùng đã được xóa.'
        ]);
    }
}
