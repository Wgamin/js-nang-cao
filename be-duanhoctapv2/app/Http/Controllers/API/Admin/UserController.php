<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /** GET /admin/users - Danh sách tất cả user theo role */
    public function index(Request $request): JsonResponse
    {
        $role = $request->query('role');
        $query = User::with('roles')->latest();

        if ($role) {
            $query->role($role);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'address' => $u->address,
                'roles' => $u->getRoleNames(),
                'created_at' => $u->created_at,
            ]),
        ]);
    }

    /** POST /admin/users - Tạo user mới */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'role'     => ['required', Rule::in(['Admin', 'Teacher', 'Student'])],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return response()->json([
            'status' => 'success',
            'data' => $user->load('roles'),
        ], 201);
    }

    /** GET /admin/users/{user} - Chi tiết user */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => array_merge($user->toArray(), [
                'roles' => $user->getRoleNames(),
            ]),
        ]);
    }

    /** PUT /admin/users/{user} - Cập nhật user */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'role'     => ['sometimes', Rule::in(['Admin', 'Teacher', 'Student'])],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $role = $data['role'] ?? null;
        unset($data['role']);
        $user->update($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return response()->json([
            'status' => 'success',
            'data' => array_merge($user->fresh()->toArray(), [
                'roles' => $user->fresh()->getRoleNames(),
            ]),
        ]);
    }

    /** DELETE /admin/users/{user} - Xóa user */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['status' => 'success', 'data' => 'Đã xóa user thành công!']);
    }
}
