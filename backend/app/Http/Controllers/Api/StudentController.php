<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    /**
     * Lấy danh sách học sinh dựa trên vai trò của người dùng.
     * Đối với Parent: Chỉ lấy con cái của họ.
     */
    public function getMyChildren(Request $request): AnonymousResourceCollection|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        // 1. Kiểm tra nếu là Phụ huynh
        if ($user->role === 'parent') {
            $guardianId = optional($user->guardian)->id;

            $children = Student::query()
                ->with(['guardian']) // Eager load để tránh lỗi N+1
                ->where('guardian_id', $guardianId)
                ->latest()
                ->get();

            return StudentResource::collection($children);
        }

        // 2. Nếu là Admin, trả về toàn bộ
        if ($user->role === 'admin') {
            return StudentResource::collection(Student::with(['guardian'])->paginate(10));
        }

        // 3. Các role khác không có quyền
        return response()->json([
            'status' => 'error',
            'message' => 'Bạn không có quyền truy cập danh sách học sinh này.'
        ], 403);
    }
}