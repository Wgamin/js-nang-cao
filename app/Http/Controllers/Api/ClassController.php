<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClassController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $classes = Classes::query()
            ->with(['subject', 'teacher'])
            ->latest('id')
            ->paginate(10);

        return ClassResource::collection($classes);
    }

    public function store(StoreClassRequest $request): ClassResource
    {
        $class = Classes::query()->create($request->validated());

        return new ClassResource($class->load(['subject', 'teacher']));
    }

    public function show(Classes $class): ClassResource
    {
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
