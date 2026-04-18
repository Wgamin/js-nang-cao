<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserTuitionController extends Controller
{
    /**
     * GET /tuitions/my-history
     * Học sinh: Xem học phí của bản thân
     * Phụ huynh: Xem học phí của tất cả con cái
     */
    public function myHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('Parent')) {
            // Phụ huynh: lấy học phí của các con
            $data = $user->children()->with([
                'tuitions.studyClass:id,name',
            ])->get()->map(fn($child) => [
                'child'    => ['id' => $child->id, 'name' => $child->name],
                'tuitions' => $child->tuitions->map(fn($t) => [
                    'id'           => $t->id,
                    'class_name'   => $t->studyClass->name ?? '—',
                    'amount'       => $t->amount,
                    'amount_paid'  => $t->amount_paid,
                    'status'       => $t->status,
                    'payment_date' => $t->payment_date,
                    'note'         => $t->note,
                ]),
            ]);

            return response()->json(['status' => 'success', 'data' => $data]);
        }

        // Học sinh: lấy học phí của mình
        $tuitions = $user->tuitions()
            ->with('studyClass:id,name')
            ->latest()
            ->get()
            ->map(fn($t) => [
                'id'           => $t->id,
                'class_name'   => $t->studyClass->name ?? '—',
                'amount'       => $t->amount,
                'amount_paid'  => $t->amount_paid,
                'status'       => $t->status,
                'payment_date' => $t->payment_date,
                'note'         => $t->note,
            ]);

        return response()->json(['status' => 'success', 'data' => $tuitions]);
    }
}
