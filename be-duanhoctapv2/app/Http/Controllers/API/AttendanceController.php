<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    /**
     * GET /schedules/{schedule}/attendance
     * Trả về danh sách học sinh của lớp + trạng thái điểm danh hiện tại.
     * Dùng cho: Giáo viên, Admin
     */
    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load(['studyClass.students', 'attendances']);

        $students = $schedule->studyClass->students->map(function ($student) use ($schedule) {
            $attendance = $schedule->attendances->firstWhere('student_id', $student->id);
            return [
                'id'         => $student->id,
                'name'       => $student->name,
                'email'      => $student->email,
                'is_present' => $attendance ? (bool) $attendance->is_present : false,
                'status'     => $attendance->status ?? 'absent',
                'note'       => $attendance->note ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'schedule' => $schedule,
                'students' => $students,
            ],
        ]);
    }

    /**
     * POST /schedules/{schedule}/attendance
     * Giáo viên lưu điểm danh hàng loạt (status: present/absent/late + note).
     */
    public function store(Request $request, Schedule $schedule): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasRole('Admin') && $schedule->studyClass->teacher_id !== $user->id) {
            return response()->json(['message' => 'Bạn không được phép điểm danh lớp này.'], 403);
        }

        $request->validate([
            'attendances'              => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status'     => 'required|in:present,absent',
            'attendances.*.note'       => 'nullable|string|max:500',
        ]);

        foreach ($request->attendances as $record) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'student_id'  => $record['student_id'],
                ],
                [
                    'status'     => $record['status'],
                    'is_present' => $record['status'] === 'present',
                    'note'       => $record['note'] ?? null,
                ]
            );
        }

        return response()->json(['status' => 'success', 'data' => 'Điểm danh thành công!']);
    }

    /**
     * GET /attendance/history
     * Học sinh: xem lịch sử điểm danh của bản thân.
     * Phụ huynh: xem lịch sử điểm danh của tất cả con mình.
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('Parent')) {
            // Phụ huynh: lấy điểm danh của các con
            $data = $user->children()->with([
                'attendances.schedule.studyClass',
            ])->get()->map(fn($child) => [
                'child'       => ['id' => $child->id, 'name' => $child->name],
                'attendances' => $child->attendances->map(fn($a) => [
                    'id'         => $a->id,
                    'status'     => $a->status,
                    'note'       => $a->note,
                    'schedule'   => [
                        'id'         => $a->schedule->id,
                        'start_time' => $a->schedule->start_time,
                        'room'       => $a->schedule->room,
                        'class_name' => $a->schedule->studyClass->name ?? '—',
                    ],
                ]),
            ]);

            return response()->json(['status' => 'success', 'data' => $data]);
        }

        // Học sinh: xem điểm danh của bản thân
        $attendances = $user->attendances()
            ->with('schedule.studyClass')
            ->latest()
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'status'     => $a->status,
                'note'       => $a->note,
                'schedule'   => [
                    'id'         => $a->schedule->id,
                    'start_time' => $a->schedule->start_time,
                    'end_time'   => $a->schedule->end_time,
                    'room'       => $a->schedule->room,
                    'class_name' => $a->schedule->studyClass->name ?? '—',
                ],
            ]);

        return response()->json(['status' => 'success', 'data' => $attendances]);
    }
}
