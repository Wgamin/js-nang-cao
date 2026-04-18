<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Classes;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    public function store(StoreEnrollmentRequest $request): EnrollmentResource
    {
        $validated = $request->validated();

        $class = Classes::query()->findOrFail($validated['class_id']);

        $enrollment = Enrollment::query()->firstOrCreate(
            [
                'student_id' => $validated['student_id'],
                'class_id' => $validated['class_id'],
            ],
            [
                'actual_price' => $validated['actual_price'] ?? $class->base_price,
                'enrolled_at' => $validated['enrolled_at'] ?? now()->toDateString(),
            ]
        );

        return new EnrollmentResource($enrollment);
    }
}
