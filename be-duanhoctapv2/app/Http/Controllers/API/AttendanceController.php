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
     * Trả về danh sách học sinh của lớp trong buổi học đó,
     * kèm trạng thái điểm danh (nếu đã có).
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
                'note'       => $attendance->note ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'schedule'  => $schedule,
                'students'  => $students,
            ],
        ]);
    }

    /**
     * POST /schedules/{schedule}/attendance
     * Giáo viên điểm danh một buổi học (cần policy: dạy lớp đó).
     */
    public function store(Request $request, Schedule $schedule): JsonResponse
    {
        Gate::authorize('takeAttendance', $schedule);

        $request->validate([
            'attendances'               => 'required|array',
            'attendances.*.student_id'  => 'required|exists:users,id',
            'attendances.*.is_present'  => 'required|boolean',
            'attendances.*.note'        => 'nullable|string|max:500',
        ]);

        foreach ($request->attendances as $record) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'student_id'  => $record['student_id'],
                ],
                [
                    'is_present' => $record['is_present'],
                    'note'       => $record['note'] ?? null,
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'data'   => 'Điểm danh thành công!',
        ]);
    }
}
