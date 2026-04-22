<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudySlot;

class StudySlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['name' => 'Ca 1 (Sáng)', 'start_time' => '07:30:00', 'end_time' => '09:30:00', 'description' => 'Ca học buổi sáng sớm'],
            ['name' => 'Ca 2 (Sáng)', 'start_time' => '09:45:00', 'end_time' => '11:45:00', 'description' => 'Ca học giữa buổi sáng'],
            ['name' => 'Ca 3 (Chiều)', 'start_time' => '13:30:00', 'end_time' => '15:30:00', 'description' => 'Ca học đầu giờ chiều'],
            ['name' => 'Ca 4 (Chiều)', 'start_time' => '15:45:00', 'end_time' => '17:45:00', 'description' => 'Ca học cuối giờ chiều'],
            ['name' => 'Ca 5 (Tối)',   'start_time' => '18:00:00', 'end_time' => '20:00:00', 'description' => 'Ca học tối (Kíp 1)'],
            ['name' => 'Ca 6 (Tối)',   'start_time' => '20:15:00', 'end_time' => '22:15:00', 'description' => 'Ca học tối (Kíp 2)'],
        ];

        foreach ($slots as $slot) {
            StudySlot::updateOrCreate(
                ['name' => $slot['name']],
                $slot
            );
        }
    }
}
