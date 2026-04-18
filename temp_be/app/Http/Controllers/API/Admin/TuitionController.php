<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tuition;
use App\Models\User;
use App\Models\StudyClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TuitionController extends Controller
{
    /**
     * GET /admin/tuitions - Danh sách học phí (có thể lọc theo class hoặc student)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tuition::with([
            'student:id,name,email,phone',
            'studyClass:id,name,subject_id',
            'studyClass.subject:id,name',
        ]);

        if ($request->has('class_id')) {
            $query->where('study_class_id', $request->class_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $query->latest()->get(),
        ]);
    }

    /**
     * POST /admin/tuitions - Tạo hóa đơn học phí thủ công
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'study_class_id' => 'required|exists:classes,id',
            'amount'         => 'required|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'status'         => 'nullable|in:unpaid,partially_paid,paid',
            'payment_date'   => 'nullable|date',
            'note'           => 'nullable|string',
        ]);

        // Tự động tính status nếu không truyền
        $amountPaid = $data['amount_paid'] ?? 0;
        if (!isset($data['status'])) {
            $data['status'] = $amountPaid >= $data['amount'] ? 'paid'
                : ($amountPaid > 0 ? 'partially_paid' : 'unpaid');
        }
        $data['amount_paid'] = $amountPaid;

        $tuition = Tuition::create($data);

        return response()->json([
            'status' => 'success',
            'data'   => $tuition->load(['student:id,name,email', 'studyClass.subject']),
        ], 201);
    }

    /**
     * GET /admin/tuitions/{tuition} - Chi tiết học phí
     */
    public function show(Tuition $tuition): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $tuition->load(['student', 'studyClass.subject']),
        ]);
    }

    /**
     * PUT /admin/tuitions/{tuition} - Xác nhận đóng tiền / cập nhật học phí
     */
    public function update(Request $request, Tuition $tuition): JsonResponse
    {
        $data = $request->validate([
            'amount'       => 'sometimes|numeric|min:0',
            'amount_paid'  => 'sometimes|numeric|min:0',
            'payment_date' => 'nullable|date',
            'note'         => 'nullable|string',
            'status'       => 'nullable|in:unpaid,partially_paid,paid',
        ]);

        // Tự động cập nhật status dựa trên số tiền
        $amount = $data['amount'] ?? $tuition->amount;
        $amountPaid = $data['amount_paid'] ?? $tuition->amount_paid;

        if (!isset($data['status'])) {
            $data['status'] = $amountPaid >= $amount ? 'paid'
                : ($amountPaid > 0 ? 'partially_paid' : 'unpaid');
        }

        $tuition->update($data);

        return response()->json([
            'status' => 'success',
            'data'   => $tuition->fresh()->load(['student:id,name,email', 'studyClass.subject']),
        ]);
    }

    /**
     * DELETE /admin/tuitions/{tuition}
     */
    public function destroy(Tuition $tuition): JsonResponse
    {
        $tuition->delete();
        return response()->json(['status' => 'success', 'data' => 'Đã xóa hóa đơn học phí!']);
    }

    /**
     * POST /admin/tuitions/generate-for-class
     * Tự động tạo hóa đơn học phí cho tất cả học sinh trong lớp
     */
    public function generateForClass(Request $request): JsonResponse
    {
        $data = $request->validate([
            'study_class_id' => 'required|exists:classes,id',
            'amount'         => 'required|numeric|min:0',
            'note'           => 'nullable|string',
        ]);

        $class = StudyClass::with('students')->findOrFail($data['study_class_id']);
        $created = 0;

        foreach ($class->students as $student) {
            // Chỉ tạo nếu chưa có hóa đơn cho học sinh này trong lớp này
            $exists = Tuition::where('user_id', $student->id)
                ->where('study_class_id', $data['study_class_id'])
                ->exists();

            if (!$exists) {
                Tuition::create([
                    'user_id'        => $student->id,
                    'study_class_id' => $data['study_class_id'],
                    'amount'         => $data['amount'],
                    'amount_paid'    => 0,
                    'status'         => 'unpaid',
                    'note'           => $data['note'] ?? null,
                ]);
                $created++;
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => "Đã tạo $created hóa đơn học phí cho lớp {$class->name}!",
        ]);
    }

    /**
     * GET /admin/tuitions/stats
     * Thống kê học phí toàn hệ thống
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'total'           => Tuition::count(),
                'unpaid'          => Tuition::where('status', 'unpaid')->count(),
                'partially_paid'  => Tuition::where('status', 'partially_paid')->count(),
                'paid'            => Tuition::where('status', 'paid')->count(),
                'total_amount'    => Tuition::sum('amount'),
                'total_collected' => Tuition::sum('amount_paid'),
            ],
        ]);
    }
}
