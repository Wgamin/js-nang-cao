<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TeacherController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TeacherResource::collection(Teacher::query()->latest('id')->paginate(10));
    }

    public function store(StoreTeacherRequest $request): TeacherResource
    {
        $teacher = Teacher::query()->create($request->validated());

        return new TeacherResource($teacher);
    }

    public function show(Teacher $teacher): TeacherResource
    {
        return new TeacherResource($teacher);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): TeacherResource
    {
        $teacher->update($request->validated());

        return new TeacherResource($teacher->refresh());
    }

    public function destroy(Teacher $teacher): Response
    {
        $teacher->delete();

        return response()->noContent();
    }
}
