<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\ClassController as AdminClassController;
use App\Http\Controllers\API\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\API\Admin\TuitionController;
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

    // Schedules (Teacher, Student, Parent)
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);
    Route::get('/tuitions/history', [\App\Http\Controllers\API\UserTuitionController::class, 'myHistory']);
    Route::get('/schedules/{schedule}/attendance', [AttendanceController::class, 'show']);
    Route::post('/schedules/{schedule}/attendance', [AttendanceController::class, 'store']);

    // Teacher
    Route::get('/teacher/classes', [ScheduleController::class, 'teacherClasses'])
        ->middleware('role:Teacher');
    Route::get('/teacher/classes/{class}/attendance', [ScheduleController::class, 'classAttendance'])
        ->middleware('role:Teacher');

    // ==== Admin-only ====
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        Route::get('/stats', [ScheduleController::class, 'stats']);

        // Users CRUD
        Route::get('/parents-list', [AdminUserController::class, 'parentsList']);
        Route::apiResource('/users', AdminUserController::class);

        // Classes CRUD + helpers
        Route::get('/teachers', [AdminClassController::class, 'teachers']);
        Route::get('/students-list', [AdminClassController::class, 'studentsList']);
        Route::apiResource('/classes', AdminClassController::class);

        // Schedules CRUD + helpers
        Route::get('/classes-list', [AdminScheduleController::class, 'classesList']);
        Route::apiResource('/schedules', AdminScheduleController::class);

        // Tuitions
        Route::get('/tuitions/stats', [TuitionController::class, 'stats']);
        Route::post('/tuitions/generate-for-class', [TuitionController::class, 'generateForClass']);
        Route::apiResource('/tuitions', TuitionController::class);

        // Excel Export/Import
        Route::get('/export', [\App\Http\Controllers\API\Admin\ExportImportController::class, 'export']);
        Route::post('/import', [\App\Http\Controllers\API\Admin\ExportImportController::class, 'import']);

        // Subjects & Rooms
        Route::apiResource('/subjects', \App\Http\Controllers\API\Admin\SubjectController::class);
        Route::apiResource('/rooms', \App\Http\Controllers\API\Admin\RoomController::class);
    });
});
