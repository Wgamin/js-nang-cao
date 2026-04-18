<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /** GET /admin/classes - Danh sách lớp học */
    public function index(): JsonResponse
    {
        $classes = StudyClass::with(['teacher:id,name,email', 'students:id,name', 'schedules', 'subject'])
            ->withCount('students')
            ->latest()
            ->get();

        return response()->json(['status' => 'success', 'data' => $classes]);
    }

    /** POST /admin/classes - Tạo lớp mới */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'teacher_id'  => 'required|exists:users,id',
            'subject_id'  => 'required|exists:subjects,id',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $class = StudyClass::create([
            'name'        => $data['name'],
            'teacher_id'  => $data['teacher_id'],
            'subject_id'  => $data['subject_id'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'active',
        ]);

        if (!empty($data['student_ids'])) {
            $class->students()->attach($data['student_ids']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $class->load(['teacher:id,name', 'students:id,name', 'subject']),
        ], 201);
    }

    /** GET /admin/classes/{class} - Chi tiết lớp */
    public function show(StudyClass $class): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $class->load(['teacher:id,name,email', 'students:id,name,email', 'schedules', 'subject']),
        ]);
    }

    /** PUT /admin/classes/{class} - Cập nhật lớp */
    public function update(Request $request, StudyClass $class): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'teacher_id'  => 'sometimes|exists:users,id',
            'subject_id'  => 'sometimes|exists:subjects,id',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $studentIds = $data['student_ids'] ?? null;
        unset($data['student_ids']);

        $class->update($data);

        if ($studentIds !== null) {
            $class->students()->sync($studentIds);
        }

        return response()->json([
            'status' => 'success',
            'data' => $class->fresh()->load(['teacher:id,name', 'students:id,name', 'subject']),
        ]);
    }

    /** DELETE /admin/classes/{class} - Xóa lớp */
    public function destroy(StudyClass $class): JsonResponse
    {
        $class->delete();
        return response()->json(['status' => 'success', 'data' => 'Đã xóa lớp thành công!']);
    }

    /** GET /admin/teachers - Danh sách giáo viên (cho dropdown) */
    public function teachers(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => User::role('Teacher')->select('id', 'name', 'email')->get(),
        ]);
    }

    /** GET /admin/students-list - Danh sách học sinh (cho dropdown) */
    public function studentsList(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => User::role('Student')->select('id', 'name', 'email')->get(),
        ]);
    }
}
