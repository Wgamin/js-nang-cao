<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // Eager load quan hệ user để tránh lỗi N+1
        return TeacherResource::collection(Teacher::query()->with('user')->latest('id')->paginate(10));
    }

    public function store(Request $request): TeacherResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // Bắt buộc khi tạo mới
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            // 1. Tạo tài khoản User với mật khẩu được nhập
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
            ]);

            // 2. Tạo bản ghi Giáo viên
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'specialization' => $validated['specialization'],
            ]);

            return new TeacherResource($teacher->load('user'));
        });
    }

    public function update(Request $request, Teacher $teacher): TeacherResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($teacher->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6', // Không bắt buộc khi update
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $teacher) {
            // 1. Cập nhật User
            if ($teacher->user_id) {
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];
                
                // Chỉ cập nhật mật khẩu nếu có nhập mới
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $teacher->user()->update($userData);
            }

            // 2. Cập nhật thông tin Giáo viên
            $teacher->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'specialization' => $validated['specialization'],
            ]);

            return new TeacherResource($teacher->refresh()->load('user'));
        });
    }

    public function destroy(Teacher $teacher): Response
    {
        $teacher->delete();

        return response()->noContent();
    }
}
