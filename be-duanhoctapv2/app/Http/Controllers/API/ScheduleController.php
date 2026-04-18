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
     * GET /schedules
     * - Admin: Xem tất cả
     * - Teacher: Xem lịch lớp mình dạy
     * - Student: Xem lịch lớp mình học
     * - Parent: Xem lịch của tất cả con mình
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('Student')) {
            $schedules = $user->enrolledClasses()
                ->with(['schedules.studyClass'])
                ->get()
                ->pluck('schedules')
                ->flatten()
                ->map(fn($s) => $s->load('studyClass'));

        } elseif ($user->hasRole('Teacher')) {
            $schedules = $user->teachingClasses()
                ->with(['schedules.studyClass'])
                ->get()
                ->pluck('schedules')
                ->flatten()
                ->map(fn($s) => $s->load('studyClass'));

        } elseif ($user->hasRole('Parent')) {
            // Phụ huynh: gom lịch học của tất cả con
            $schedules = $user->children()
                ->with(['enrolledClasses.schedules.studyClass'])
                ->get()
                ->flatMap(fn($child) => $child->enrolledClasses
                    ->flatMap(fn($cls) => $cls->schedules->map(fn($s) => array_merge(
                        $s->toArray(),
                        ['child_name' => $child->name, 'class_name' => $cls->name]
                    )))
                );

            return response()->json(['status' => 'success', 'data' => $schedules->values()]);

        } else {
            $schedules = Schedule::with('studyClass')->latest('start_time')->get();
        }

        return response()->json(['status' => 'success', 'data' => $schedules->values()]);
    }

    /**
     * GET /teacher/classes - Giáo viên: Lấy lớp + học sinh + lịch
     */
    public function teacherClasses(Request $request): JsonResponse
    {
        $classes = $request->user()->teachingClasses()
            ->with(['students:id,name,email', 'schedules'])
            ->get();

        return response()->json(['status' => 'success', 'data' => $classes]);
    }

    /**
     * GET /admin/stats
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_students' => User::role('Student')->count(),
                'total_teachers' => User::role('Teacher')->count(),
                'total_parents'  => User::role('Parent')->count(),
                'total_classes'  => \App\Models\StudyClass::count(),
                'total_schedules' => Schedule::count(),
            ],
        ]);
    }
}
