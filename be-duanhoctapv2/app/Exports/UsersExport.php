<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with('roles')->get();
    }

    public function headings(): array
    {
        return [
            'Tên',
            'Email',
            'Mật khẩu',
            'Số điện thoại',
            'Địa chỉ',
            'Vai trò',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            '', // Mật khẩu không xuất ra, để trống để người dùng tự điền nếu muốn tạo mới/update
            $user->phone,
            $user->address,
            count($user->roles) > 0 ? $user->roles[0]->name : 'User',
        ];
    }
}
