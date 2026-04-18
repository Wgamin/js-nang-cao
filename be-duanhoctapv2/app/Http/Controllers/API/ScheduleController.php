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
     * GET /teacher/classes/{class}/attendance
     * Giáo viên: Lấy chi tiết lớp với tất cả buổi học đã đánh số
     * Mỗi buổi có attendance_count và trạng thái đã điểm danh chưa
     */
    public function classAttendance(Request $request, $classId): JsonResponse
    {
        $studyClass = $request->user()->teachingClasses()
            ->where('classes.id', $classId)
            ->with(['students:id,name,email', 'schedules' => function ($q) {
                $q->orderBy('start_time');
            }])
            ->first();

        if (!$studyClass) {
            return response()->json(['message' => 'Không tìm thấy lớp hoặc bạn không có quyền.'], 403);
        }

        // Lấy danh sách tất cả học sinh đã điểm danh cho mỗi buổi
        $schedules = $studyClass->schedules->map(function ($schedule) {
            $attendances = $schedule->attendances()->with('student:id,name,email')->get();
            $presentCount = $attendances->where('is_present', true)->count();
            $absentCount = $attendances->where('is_present', false)->count();
            $totalStudents = $attendances->count();

            return [
                'id'              => $schedule->id,
                'attendance_count'=> $schedule->attendance_count,
                'session_label'   => 'Buổi ' . $schedule->attendance_count,
                'start_time'      => $schedule->start_time,
                'end_time'        => $schedule->end_time,
                'room'            => $schedule->room,
                'note'            => $schedule->note,
                'is_attendanced'  => $totalStudents > 0,
                'stats'           => [
                    'present' => $presentCount,
                    'absent'  => $absentCount,
                    'total'   => $totalStudents,
                ],
                'attendances'     => $attendances->map(fn($a) => [
                    'student_id'  => $a->student_id,
                    'student_name'=> $a->student->name ?? 'N/A',
                    'is_present'  => $a->is_present,
                    'status'      => $a->status,
                    'note'        => $a->note,
                ]),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'class'     => [
                    'id'          => $studyClass->id,
                    'name'        => $studyClass->name,
                    'subject'     => $studyClass->subject->name ?? 'N/A',
                    'total_students' => $studyClass->students->count(),
                ],
                'schedules' => $schedules,
            ],
        ]);
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
