<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StudyClass;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ===== 1. Tạo Roles =====
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // ===== 2. Tạo Users mẫu =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole($adminRole);

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            ['name' => 'Nguyễn Văn Thầy', 'password' => Hash::make('password')]
        );
        $teacher->assignRole($teacherRole);

        $teacher2 = User::firstOrCreate(
            ['email' => 'teacher2@test.com'],
            ['name' => 'Trần Thị Cô', 'password' => Hash::make('password')]
        );
        $teacher2->assignRole($teacherRole);

        $student = User::firstOrCreate(
            ['email' => 'student@test.com'],
            ['name' => 'Lê Văn Học Sinh', 'password' => Hash::make('password')]
        );
        $student->assignRole($studentRole);

        $student2 = User::firstOrCreate(
            ['email' => 'student2@test.com'],
            ['name' => 'Phạm Thị Sinh Viên', 'password' => Hash::make('password')]
        );
        $student2->assignRole($studentRole);

        $student3 = User::firstOrCreate(
            ['email' => 'student3@test.com'],
            ['name' => 'Hoàng Minh Trí', 'password' => Hash::make('password')]
        );
        $student3->assignRole($studentRole);

        // ===== 3. Tạo Lớp học mẫu =====
        $class1 = StudyClass::firstOrCreate(
            ['name' => 'Lớp Toán 12'],
            ['teacher_id' => $teacher->id]
        );

        $class2 = StudyClass::firstOrCreate(
            ['name' => 'Lớp Lý 11'],
            ['teacher_id' => $teacher->id]
        );

        $class3 = StudyClass::firstOrCreate(
            ['name' => 'Lớp Anh Văn 10'],
            ['teacher_id' => $teacher2->id]
        );

        // ===== 4. Gán học sinh vào lớp =====
        $class1->students()->syncWithoutDetaching([$student->id, $student2->id, $student3->id]);
        $class2->students()->syncWithoutDetaching([$student->id, $student2->id]);
        $class3->students()->syncWithoutDetaching([$student2->id, $student3->id]);

        // ===== 5. Tạo lịch học mẫu =====
        Schedule::firstOrCreate([
            'class_id' => $class1->id,
            'start_time' => now()->addDays(1)->setTime(18, 0, 0),
            'room' => 'Phòng 101',
        ]);

        Schedule::firstOrCreate([
            'class_id' => $class1->id,
            'start_time' => now()->addDays(3)->setTime(18, 0, 0),
            'room' => 'Phòng 101',
        ]);

        Schedule::firstOrCreate([
            'class_id' => $class2->id,
            'start_time' => now()->addDays(2)->setTime(19, 30, 0),
            'room' => 'Phòng 203',
        ]);

        Schedule::firstOrCreate([
            'class_id' => $class3->id,
            'start_time' => now()->addDays(1)->setTime(8, 0, 0),
            'room' => 'Phòng 305',
        ]);

        Schedule::firstOrCreate([
            'class_id' => $class3->id,
            'start_time' => now()->addDays(4)->setTime(8, 0, 0),
            'room' => 'Phòng 305',
        ]);
    }
}
