<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\StudyClass;
use App\Models\Tuition;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ===== 1. Tạo Roles =====
        $adminRole   = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $parentRole  = Role::firstOrCreate(['name' => 'Parent']);

        // ===== 2. Tạo Users =====
        $admin = User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin User', 'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        $teacher = User::firstOrCreate(['email' => 'teacher@test.com'], [
            'name' => 'Nguyễn Văn Thầy', 'password' => Hash::make('password'), 'phone' => '0901234567',
        ]);
        $teacher->assignRole($teacherRole);

        $teacher2 = User::firstOrCreate(['email' => 'teacher2@test.com'], [
            'name' => 'Trần Thị Cô', 'password' => Hash::make('password'), 'phone' => '0901234568',
        ]);
        $teacher2->assignRole($teacherRole);

        // Phụ huynh
        $parent = User::firstOrCreate(['email' => 'parent@test.com'], [
            'name' => 'Phụ Huynh Nguyễn', 'password' => Hash::make('password'),
            'phone' => '0912345678', 'address' => '123 Nguyễn Trãi, Quận 1',
        ]);
        $parent->assignRole($parentRole);

        // Học sinh (liên kết phụ huynh)
        $student = User::firstOrCreate(['email' => 'student@test.com'], [
            'name' => 'Nguyễn Văn An', 'password' => Hash::make('password'),
            'parent_id' => $parent->id,
        ]);
        $student->assignRole($studentRole);

        $student2 = User::firstOrCreate(['email' => 'student2@test.com'], [
            'name' => 'Nguyễn Thị Bình', 'password' => Hash::make('password'),
            'parent_id' => $parent->id,
        ]);
        $student2->assignRole($studentRole);

        $student3 = User::firstOrCreate(['email' => 'student3@test.com'], [
            'name' => 'Hoàng Minh Trí', 'password' => Hash::make('password'),
        ]);
        $student3->assignRole($studentRole);

        // ===== 2.5 Tạo Môn Học =====
        $subjectMath = \App\Models\Subject::firstOrCreate(['name' => 'Toán Học'], ['description' => 'Toán Trung học & Đại học']);
        $subjectPhys = \App\Models\Subject::firstOrCreate(['name' => 'Vật Lý'], ['description' => 'Vật Lý nền tảng & Nâng cao']);

        // ===== 3. Tạo Lớp học =====
        $class1 = StudyClass::firstOrCreate(['name' => 'Lớp Toán 12'], [
            'teacher_id' => $teacher->id, 'description' => 'Ôn thi đại học', 'status' => 'active', 'subject_id' => $subjectMath->id,
        ]);
        $class2 = StudyClass::firstOrCreate(['name' => 'Lớp Lý 11'], [
            'teacher_id' => $teacher->id, 'description' => 'Nâng cao Vật Lý', 'status' => 'active', 'subject_id' => $subjectPhys->id,
        ]);
        $class3 = StudyClass::firstOrCreate(['name' => 'Lớp Anh Văn 10'], [
            'teacher_id' => $teacher2->id, 'description' => 'Giao tiếp cơ bản', 'status' => 'active',
        ]);

        // ===== 4. Gán học sinh vào lớp =====
        $class1->students()->syncWithoutDetaching([$student->id, $student2->id, $student3->id]);
        $class2->students()->syncWithoutDetaching([$student->id, $student2->id]);
        $class3->students()->syncWithoutDetaching([$student2->id, $student3->id]);

        // ===== 5. Tạo lịch học =====
        $sched1 = Schedule::firstOrCreate([
            'class_id' => $class1->id, 'start_time' => now()->subDays(2)->setTime(18, 0),
        ], ['end_time' => now()->subDays(2)->setTime(20, 0), 'room' => 'Phòng 101', 'note' => 'Buổi học ôn tập']);

        $sched2 = Schedule::firstOrCreate([
            'class_id' => $class1->id, 'start_time' => now()->addDays(1)->setTime(18, 0),
        ], ['end_time' => now()->addDays(1)->setTime(20, 0), 'room' => 'Phòng 101']);

        $sched3 = Schedule::firstOrCreate([
            'class_id' => $class2->id, 'start_time' => now()->addDays(2)->setTime(19, 30),
        ], ['end_time' => now()->addDays(2)->setTime(21, 0), 'room' => 'Phòng 203']);

        $sched4 = Schedule::firstOrCreate([
            'class_id' => $class3->id, 'start_time' => now()->addDays(1)->setTime(8, 0),
        ], ['end_time' => now()->addDays(1)->setTime(10, 0), 'room' => 'Phòng 305']);

        // ===== 6. Tạo điểm danh mẫu (buổi đã qua) =====
        $statuses = ['present', 'present', 'late'];
        foreach ($class1->students as $i => $st) {
            Attendance::firstOrCreate([
                'schedule_id' => $sched1->id,
                'student_id'  => $st->id,
            ], [
                'status'     => $statuses[$i] ?? 'absent',
                'is_present' => ($statuses[$i] ?? 'absent') === 'present',
                'note'       => ($statuses[$i] ?? '') === 'late' ? 'Đến muộn 10 phút' : null,
            ]);
        }

        // ===== 7. Tạo học phí mẫu =====
        $tuitionData = [
            ['student' => $student,  'class' => $class1, 'amount' => 800000, 'paid' => 800000, 'status' => 'paid',           'note' => 'Đóng tiền mặt ngày ' . now()->subDays(5)->format('d/m/Y')],
            ['student' => $student,  'class' => $class2, 'amount' => 700000, 'paid' => 350000, 'status' => 'partially_paid', 'note' => 'Chuyển khoản MB Bank'],
            ['student' => $student2, 'class' => $class1, 'amount' => 800000, 'paid' => 0,      'status' => 'unpaid',         'note' => null],
            ['student' => $student2, 'class' => $class3, 'amount' => 600000, 'paid' => 600000, 'status' => 'paid',           'note' => 'Đóng đủ tiền mặt'],
            ['student' => $student3, 'class' => $class1, 'amount' => 800000, 'paid' => 0,      'status' => 'unpaid',         'note' => null],
            ['student' => $student3, 'class' => $class3, 'amount' => 600000, 'paid' => 200000, 'status' => 'partially_paid', 'note' => 'Đóng một phần'],
        ];

        foreach ($tuitionData as $t) {
            Tuition::firstOrCreate(
                ['user_id' => $t['student']->id, 'study_class_id' => $t['class']->id],
                [
                    'amount'       => $t['amount'],
                    'amount_paid'  => $t['paid'],
                    'status'       => $t['status'],
                    'payment_date' => $t['paid'] > 0 ? now()->subDays(rand(1, 10)) : null,
                    'note'         => $t['note'],
                ]
            );
        }

        // ===== 8. Tạo Ca học mẫu =====
        $this->call(StudySlotSeeder::class);
    }
}
