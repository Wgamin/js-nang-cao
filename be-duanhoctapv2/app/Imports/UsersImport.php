<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class UsersImport implements ToCollection
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new Exception("File Excel trống.");
        }

        $header = $rows->first()->toArray();
        $expectedHeaders = ['Tên', 'Email', 'Mật khẩu', 'Số điện thoại', 'Địa chỉ', 'Vai trò'];
        
        // Kiểm tra xem header có đủ và đúng thứ tự hay không
        foreach ($expectedHeaders as $index => $expected) {
            if (!isset($header[$index]) || trim((string)$header[$index]) !== $expected) {
                throw new Exception("Lỗi định dạng file Excel! Cột thứ ".($index + 1)." phải là '$expected'. Vui lòng Click 'Xuất Excel' để lấy file mẫu chuẩn.");
            }
        }

        foreach ($rows as $index => $row) {
            // Bỏ qua dòng tiêu đề
            if ($index === 0) {
                continue;
            }

            // Bỏ qua dòng trống
            if (empty($row[0]) && empty($row[1])) {
                continue;
            }

            if (empty($row[0])) {
                throw new Exception("Lỗi ở dòng ".($index + 1).": Tên không được để trống.");
            }
            if (empty($row[1])) {
                throw new Exception("Lỗi ở dòng ".($index + 1).": Email không được để trống.");
            }

            // Kiểm tra email trùng
            if (User::where('email', $row[1])->exists()) {
                throw new Exception("Lỗi ở dòng ".($index + 1).": Email '{$row[1]}' đã tồn tại trong hệ thống.");
            }

            $user = User::create([
                'name'     => $row[0],
                'email'    => $row[1],
                'password' => Hash::make($row[2] ?? 'password'),
                'phone'    => $row[3] ?? null,
                'address'  => $row[4] ?? null,
            ]);

            // Các vai trò hợp lệ trong hệ thống
            $allowedRoles = ['Admin', 'Teacher', 'Student', 'Parent'];

            // Gán vai trò nếu có và hợp lệ
            if (!empty($row[5])) {
                $roleName = trim($row[5]);
                if (in_array($roleName, $allowedRoles)) {
                    $user->assignRole($roleName);
                } else {
                    throw new Exception("Lỗi ở dòng ".($index + 1).": Vai trò '$roleName' không hợp lệ. Các vai trò được cho phép: Admin, Teacher, Student, Parent.");
                }
            } else {
                $user->assignRole('Student');
            }
        }
    }
}
