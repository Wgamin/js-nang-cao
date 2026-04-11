<?php

use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::apiResource('subjects', SubjectController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('classes', ClassController::class);
Route::post('enrollments', [EnrollmentController::class, 'store']);
