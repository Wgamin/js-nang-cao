<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Guardian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Hệ thống Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Tạo Teacher
        $userTeacher = User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Giáo viên A',
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]
        );
        Teacher::updateOrCreate(
            ['user_id' => $userTeacher->id],
            [
                'name' => 'Giáo viên A',
                'phone' => '0123456789',
                'specialization' => 'Toán học',
            ]
        );

        // 3. Tạo Parent (Guardian)
        $userParent = User::updateOrCreate(
            ['email' => 'parent@example.com'],
            [
                'name' => 'Phụ huynh B',
                'password' => Hash::make('password'),
                'role' => 'parent',
            ]
        );
        Guardian::updateOrCreate(
            ['user_id' => $userParent->id],
            [
                'name' => 'Phụ huynh B',
                'phone' => '0987654321',
                'email' => 'parent@example.com',
                'address' => 'Hà Nội',
            ]
        );

        echo "--- Đã tạo tài khoản test thành công ---\n";
        echo "Admin: admin@example.com / password\n";
        echo "Teacher: teacher@example.com / password\n";
        echo "Parent: parent@example.com / password\n";
    }
}
