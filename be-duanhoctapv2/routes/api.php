<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\ClassController as AdminClassController;
use App\Http\Controllers\API\Admin\ScheduleController as AdminScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==== Public ====
Route::post('/login', [AuthController::class, 'login']);

// ==== Auth Required ====
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ---- Schedule & Attendance (Teacher & Student) ----
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{schedule}/attendance', [AttendanceController::class, 'show']);
    Route::post('/schedules/{schedule}/attendance', [AttendanceController::class, 'store']);

    // Teacher: danh sách lớp mình dạy
    Route::get('/teacher/classes', [ScheduleController::class, 'teacherClasses'])
        ->middleware('role:Teacher');

    // ---- Admin-only routes ----
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        // Thống kê
        Route::get('/stats', [ScheduleController::class, 'stats']);

        // Users CRUD
        Route::apiResource('/users', AdminUserController::class);

        // Classes CRUD + helpers
        Route::get('/teachers', [AdminClassController::class, 'teachers']);
        Route::get('/students-list', [AdminClassController::class, 'studentsList']);
        Route::apiResource('/classes', AdminClassController::class);

        // Schedules CRUD + helpers
        Route::get('/classes-list', [AdminScheduleController::class, 'classesList']);
        Route::apiResource('/schedules', AdminScheduleController::class);
    });
});
