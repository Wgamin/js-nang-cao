<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use Illuminate\Http\Request; // Thêm Request để lấy thông tin user
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     * Logic Data Scoping: Admin thấy tất cả, Teacher chỉ thấy lớp mình dạy.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        
        $query = Classes::query()->with(['subject', 'teacher']);

        // --- BẮT ĐẦU DATA SCOPING ---
        if ($user->role === 'teacher') {
            // Chỉ lấy các lớp mà cột teacher_id khớp với ID của user đang đăng nhập
            $query->where('teacher_id', $user->id);
        } 
        // Nếu là admin, không cần thêm điều kiện where (thấy hết)
        // --- KẾT THÚC DATA SCOPING ---

        $classes = $query->latest('id')->paginate(10);

        return ClassResource::collection($classes);
    }

    public function store(StoreClassRequest $request): ClassResource
    {
        // Chỉ Admin mới được tạo lớp (Logic này nên chặn ở Middleware trong Route)
        $class = Classes::query()->create($request->validated());

        return new ClassResource($class->load(['subject', 'teacher']));
    }

    public function show(Classes $class): ClassResource
    {
        $user = auth()->user();
        
        // Kiểm tra quyền: Nếu là Teacher, chỉ xem được lớp mình dạy
        if ($user->role === 'teacher') {
            $teacherId = optional($user->teacher)->id;
            if ($class->teacher_id !== $teacherId) {
                abort(403, 'Bạn không có quyền xem thông tin lớp học này.');
            }
        }

        return new ClassResource($class->load(['subject', 'teacher']));
    }

    public function update(UpdateClassRequest $request, Classes $class): ClassResource
    {
        $class->update($request->validated());

        return new ClassResource($class->refresh()->load(['subject', 'teacher']));
    }

    public function destroy(Classes $class): Response
    {
        $class->delete();

        return response()->noContent();
    }
}