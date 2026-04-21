<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudySlot;
use Illuminate\Http\Request;

class StudySlotController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => StudySlot::orderBy('start_time')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string',
        ]);

        $slot = StudySlot::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo ca học thành công',
            'data' => $slot
        ]);
    }

    public function show(StudySlot $studySlot)
    {
        return response()->json([
            'success' => true,
            'data' => $studySlot
        ]);
    }

    public function update(Request $request, $id)
    {
        $slot = StudySlot::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string',
        ]);

        $slot->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ca học thành công',
            'data' => $slot
        ]);
    }

    public function destroy($id)
    {
        $slot = StudySlot::findOrFail($id);
        $slot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa ca học thành công'
        ]);
    }
}
