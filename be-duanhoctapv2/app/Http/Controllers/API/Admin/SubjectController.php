<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => Subject::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:subjects',
            'description' => 'nullable|string',
        ]);
        $subject = Subject::create($data);
        return response()->json(['status' => 'success', 'data' => $subject], 201);
    }

    public function show(Subject $subject)
    {
        return response()->json(['status' => 'success', 'data' => $subject]);
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string',
        ]);
        $subject->update($data);
        return response()->json(['status' => 'success', 'data' => $subject]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã xoá thành công']);
    }
}
