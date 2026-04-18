<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\StudyClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /** GET /admin/schedules - Tất cả lịch học */
    public function index(): JsonResponse
    {
        $schedules = Schedule::with(['studyClass:id,name,teacher_id', 'studyClass.teacher:id,name'])
            ->latest('start_time')
            ->get();

        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    /** POST /admin/schedules - Tạo lịch học mới */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'room'       => 'required|string|max:100',
            'note'       => 'nullable|string|max:500',
            'attendance_count' => 'nullable|integer|min:0',
        ]);

        \Log::info('Schedule store - data received:', $data);
        
        // Tự động đánh số buổi học dựa trên số lịch đã có của lớp này
        $existingCount = Schedule::where('class_id', $data['class_id'])->count();
        $data['attendance_count'] = $existingCount + 1;
        
        $schedule = Schedule::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $schedule->load('studyClass:id,name'),
        ], 201);
    }

    /** GET /admin/schedules/{schedule} */
    public function show(Schedule $schedule): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $schedule->load(['studyClass.teacher', 'attendances.student']),
        ]);
    }

    /** PUT /admin/schedules/{schedule} */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $data = $request->validate([
            'class_id'   => 'sometimes|exists:classes,id',
            'start_time' => 'sometimes|date',
            'end_time'   => 'sometimes|date',
            'room'       => 'sometimes|string|max:100',
            'note'       => 'nullable|string|max:500',
            'attendance_count' => 'nullable|integer|min:0',
        ]);

        \Log::info('Schedule update - data received:', $data);
        
        $schedule->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $schedule->fresh()->load('studyClass:id,name'),
        ]);
    }

    /** DELETE /admin/schedules/{schedule} */
    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();
        return response()->json(['status' => 'success', 'data' => 'Đã xóa lịch học thành công!']);
    }

    /** GET /admin/classes-list - Danh sách lớp (cho dropdown) */
    public function classesList(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => StudyClass::where('status', 'active')->select('id', 'name')->get(),
        ]);
    }
}
