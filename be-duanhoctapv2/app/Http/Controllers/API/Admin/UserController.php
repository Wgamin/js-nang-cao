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
        $query = User::with(['roles', 'children:id,name,email'])->latest();

        if ($role) {
            $query->role($role);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()->map(fn($u) => [
                'id'          => $u->id,
                'name'        => $u->name,
                'email'       => $u->email,
                'phone'       => $u->phone,
                'address'     => $u->address,
                'parent_id'   => $u->parent_id,
                'roles'       => $u->getRoleNames(),
                'children'    => $u->children->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email]),
                'created_at'  => $u->created_at,
            ]),
        ]);
    }

    /** POST /admin/users - Tạo user mới */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
            'parent_id' => 'nullable|exists:users,id',
            'child_ids' => 'nullable|array',
            'child_ids.*' => 'exists:users,id',
            'role'      => ['required', Rule::in(['Admin', 'Teacher', 'Student', 'Parent'])],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone'] ?? null,
            'address'   => $data['address'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        $user->assignRole($data['role']);

        // Nếu là Phụ huynh, gán các học sinh đã chọn
        if ($data['role'] === 'Parent' && !empty($data['child_ids'])) {
            User::whereIn('id', $data['child_ids'])->update(['parent_id' => $user->id]);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $user->load(['roles', 'children:id,name,email']),
        ], 201);
    }

    /** GET /admin/parents-list - Danh sách phụ huynh (cho dropdown học sinh) */
    public function parentsList(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => User::role('Parent')->select('id', 'name', 'email')->get(),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->load([
            'roles', 
            'children.attendances.schedule.studyClass', 
            'children.tuitions.studyClass',
            'parent:id,name,email',
            'teachingClasses.schedules',
            'teachingClasses.students',
            'enrolledClasses.schedules',
            'enrolledClasses.teacher',
            'attendances.schedule.studyClass',
            'tuitions.studyClass.subject',
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => array_merge($user->toArray(), [
                'roles'    => $user->getRoleNames(),
            ]),
        ]);
    }

    /** PUT /admin/users/{user} - Cập nhật user */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password'    => 'sometimes|string|min:6',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'parent_id'   => 'nullable|exists:users,id',
            'child_ids'   => 'nullable|array',
            'child_ids.*' => 'exists:users,id',
            'role'        => ['sometimes', Rule::in(['Admin', 'Teacher', 'Student', 'Parent'])],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $role     = $data['role'] ?? null;
        $childIds = $data['child_ids'] ?? null;
        unset($data['role'], $data['child_ids']);

        $user->update($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        // Đồng bộ học sinh liên kết với phụ huynh
        if ($childIds !== null) {
            // Xóa parent_id của những học sinh trước đây thuộc phụ huynh này
            User::where('parent_id', $user->id)
                ->whereNotIn('id', $childIds)
                ->update(['parent_id' => null]);

            // Gán parent_id cho các học sinh được chọn
            if (!empty($childIds)) {
                User::whereIn('id', $childIds)->update(['parent_id' => $user->id]);
            }
        }

        $fresh = $user->fresh()->load(['roles', 'children:id,name,email']);

        return response()->json([
            'status' => 'success',
            'data'   => array_merge($fresh->toArray(), [
                'roles'    => $fresh->getRoleNames(),
                'children' => $fresh->children,
            ]),
        ]);
    }

    /** DELETE /admin/users/{user} - Xóa user */
    public function destroy(User $user): JsonResponse
    {
        // Nếu xóa phụ huynh, clear parent_id của các con
        if ($user->hasRole('Parent')) {
            User::where('parent_id', $user->id)->update(['parent_id' => null]);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'data' => 'Đã xóa user thành công!']);
    }
}
