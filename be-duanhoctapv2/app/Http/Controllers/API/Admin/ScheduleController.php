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
        ], [
            'start_time.required' => 'Thời gian bắt đầu không được để trống.',
            'start_time.date'     => 'Thời gian bắt đầu phải là một ngày giờ hợp lệ.',
            'end_time.required'   => 'Thời gian kết thúc không được để trống.',
            'end_time.date'       => 'Thời gian kết thúc phải là một ngày giờ hợp lệ.',
            'end_time.after'      => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'class_id.required'   => 'Mã lớp học không được để trống.',
            'class_id.exists'     => 'Lớp học không tồn tại.',
            'room.required'       => 'Phòng học không được để trống.',
        ]);

        // Tự động đánh số buổi học dựa trên số lịch đã có của lớp này
        $existingCount = Schedule::where('class_id', $data['class_id'])->count();
        $data['attendance_count'] = $existingCount + 1;
        
        $schedule = Schedule::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo buổi học thành công',
            'data' => $schedule->load('studyClass:id,name'),
        ], 201);
    }

    /** POST /admin/schedules/bulk - Tạo nhiều lịch học */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'schedules' => 'required|array|min:1',
            'schedules.*.class_id'   => 'required|exists:classes,id',
            'schedules.*.start_time' => 'required|date',
            'schedules.*.end_time'   => 'required|date|after:schedules.*.start_time',
            'schedules.*.room'       => 'required|string|max:100',
            'schedules.*.note'       => 'nullable|string|max:500',
        ], [
            'schedules.required' => 'Danh sách buổi học không được để trống.',
            'schedules.array'    => 'Định dạng dữ liệu không hợp lệ.',
            'schedules.min'      => 'Vui lòng chọn ít nhất 1 buổi học.',
            'schedules.*.class_id.required'   => 'Thiếu mã lớp học cho một số buổi.',
            'schedules.*.start_time.required' => 'Thiếu thời gian bắt đầu cho một số buổi.',
            'schedules.*.start_time.date'     => 'Ngày bắt đầu không hợp lệ.',
            'schedules.*.end_time.required'   => 'Thiếu thời gian kết thúc.',
            'schedules.*.end_time.after'      => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'schedules.*.room.required'       => 'Vui lòng nhập phòng học cho tất cả các buổi.',
        ]);

        $createdCount = 0;
        foreach ($request->schedules as $sData) {
            // Tự động tính số buổi
            $existingCount = Schedule::where('class_id', $sData['class_id'])->count();
            $sData['attendance_count'] = $existingCount + 1;
            Schedule::create($sData);
            $createdCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã tạo thành công $createdCount buổi học."
        ]);
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
