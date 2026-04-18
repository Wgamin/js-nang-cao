<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\StudyClass;
use Illuminate\Database\Seeder;

class UpdateAttendanceCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Cập nhật attendance_count cho tất cả các buổi học hiện có
     * theo thứ tự ngày bắt đầu của mỗi lớp
     */
    public function run(): void
    {
        $classes = StudyClass::with(['schedules' => function ($q) {
            $q->orderBy('start_time');
        }])->get();

        $updated = 0;

        foreach ($classes as $class) {
            $count = 0;
            foreach ($class->schedules as $schedule) {
                $count++;
                $schedule->update(['attendance_count' => $count]);
                $updated++;
            }
        }

        $this->command->info("Đã cập nhật attendance_count cho {$updated} buổi học.");
    }
}
