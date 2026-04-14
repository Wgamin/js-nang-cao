<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController; // Đừng quên Import này
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Yêu cầu đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // 1. Route chung cho mọi User đã đăng nhập
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    /*
     * NHÓM ADMIN: Toàn quyền quản lý hệ thống
     */
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('teachers', TeacherController::class);
        // Admin cũng có thể quản lý Guardians nếu cần
    });

    /*
     * NHÓM TEACHER & ADMIN: Liên quan đến lớp học
     */
    Route::middleware('role:admin,teacher')->group(function () {
        // ClassController đã có logic lọc (Data Scoping) trong hàm index
        Route::get('classes', [ClassController::class, 'index']);
        Route::get('classes/{class}', [ClassController::class, 'show']);
        
        // Chỉ Admin mới được tạo/sửa/xóa lớp
        Route::middleware('role:admin')->group(function () {
            Route::post('classes', [ClassController::class, 'store']);
            Route::put('classes/{class}', [ClassController::class, 'update']);
            Route::delete('classes/{class}', [ClassController::class, 'destroy']);
        });
    });

    /*
     * NHÓM PARENT: Xem con cái
     */
    Route::middleware('role:parent,admin')->group(function () {
        Route::get('/my-children', [StudentController::class, 'getMyChildren']);
    });

    /*
     * Ghi danh (Enrollments)
     */
    Route::post('enrollments', [EnrollmentController::class, 'store'])
         ->middleware('role:admin,parent'); // Cho phép admin hoặc phụ huynh đăng ký học

});