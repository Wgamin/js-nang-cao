<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SubjectResource::collection(Subject::query()->latest('id')->paginate(10));
    }

    public function store(StoreSubjectRequest $request): SubjectResource
    {
        $subject = Subject::query()->create($request->validated());

        return new SubjectResource($subject);
    }

    public function show(Subject $subject): SubjectResource
    {
        return new SubjectResource($subject);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): SubjectResource
    {
        $subject->update($request->validated());

        return new SubjectResource($subject->refresh());
    }

    public function destroy(Subject $subject): Response
    {
        $subject->delete();

        return response()->noContent();
    }
}
