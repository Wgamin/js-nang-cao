<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

// Exports
use App\Exports\UsersExport;
use App\Exports\ClassesExport;
use App\Exports\SchedulesExport;
use App\Exports\AttendancesExport;
use App\Exports\TuitionsExport;

// Imports 
use App\Imports\UsersImport;
use App\Imports\ClassesImport;
use App\Imports\SchedulesImport;
use App\Imports\AttendancesImport;
use App\Imports\TuitionsImport;

class ExportImportController extends Controller
{
    /**
     * Xuất dữ liệu ra Excel
     * Cú pháp đường dẫn: GET /api/admin/export?table=users
     */
    public function export(Request $request)
    {
        $table = $request->query('table');
        $fileName = $table . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        switch ($table) {
            case 'users':
                return Excel::download(new UsersExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, $headers);
            case 'classes':
                return Excel::download(new ClassesExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, $headers);
            case 'schedules':
                return Excel::download(new SchedulesExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, $headers);
            case 'attendances':
                return Excel::download(new AttendancesExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, $headers);
            case 'tuitions':
                return Excel::download(new TuitionsExport, $fileName, \Maatwebsite\Excel\Excel::XLSX, $headers);
            default:
                return response()->json(['status' => 'error', 'message' => 'Bảng không hợp lệ'], 400);
        }
    }

    /**
     * Nhập dữ liệu từ file Excel
     * Cú pháp đường dẫn: POST /api/admin/import?table=users (form-data: file)
     */
    public function import(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max 10MB
        ]);

        $table = $request->input('table');
        $file = $request->file('file');

        try {
            switch ($table) {
                case 'users':
                    Excel::import(new UsersImport, $file);
                    break;
                case 'classes':
                    Excel::import(new ClassesImport, $file);
                    break;
                case 'schedules':
                    Excel::import(new SchedulesImport, $file);
                    break;
                case 'attendances':
                    Excel::import(new AttendancesImport, $file);
                    break;
                case 'tuitions':
                    Excel::import(new TuitionsImport, $file);
                    break;
                default:
                    return response()->json(['status' => 'error', 'message' => 'Bảng không hợp lệ'], 400);
            }

            return response()->json(['status' => 'success', 'data' => 'Nhập dữ liệu thành công!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Lỗi nhập dữ liệu: ' . $e->getMessage()], 500);
        }
    }
}
