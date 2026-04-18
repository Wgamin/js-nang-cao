<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => Room::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:rooms',
            'capacity' => 'nullable|integer',
            'location' => 'nullable|string',
        ]);
        $room = Room::create($data);
        return response()->json(['status' => 'success', 'data' => $room], 201);
    }

    public function show(Room $room)
    {
        return response()->json(['status' => 'success', 'data' => $room]);
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|unique:rooms,name,' . $room->id,
            'capacity' => 'nullable|integer',
            'location' => 'nullable|string',
        ]);
        $room->update($data);
        return response()->json(['status' => 'success', 'data' => $room]);
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã xoá thành công']);
    }
}
