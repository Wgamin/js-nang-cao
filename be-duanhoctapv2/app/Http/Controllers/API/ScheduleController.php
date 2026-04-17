<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Lấy danh sách lịch học.
     * - Admin: Xem tất cả
     * - Teacher: Xem lịch lớp mình dạy
     * - Student: Xem lịch lớp mình học
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('Student')) {
            $schedules = $user->enrolledClasses()
                ->with(['schedules.studyClass'])
                ->get()
                ->pluck('schedules')
                ->flatten();
        } elseif ($user->hasRole('Teacher')) {
            $schedules = $user->teachingClasses()
                ->with(['schedules.studyClass'])
                ->get()
                ->pluck('schedules')
                ->flatten();
        } else {
            // Admin thấy tất cả
            $schedules = Schedule::with('studyClass')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    /**
     * Thống kê tổng quan (chỉ Admin).
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_students' => User::role('Student')->count(),
                'total_teachers' => User::role('Teacher')->count(),
                'total_classes' => \App\Models\StudyClass::count(),
                'total_schedules' => Schedule::count(),
            ]
        ]);
    }

    /**
     * Lấy danh sách lớp mà giáo viên đang dạy (kèm danh sách học sinh).
     */
    public function teacherClasses(Request $request): JsonResponse
    {
        $user = $request->user();

        $classes = $user->teachingClasses()
            ->with(['students', 'schedules'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }
}
