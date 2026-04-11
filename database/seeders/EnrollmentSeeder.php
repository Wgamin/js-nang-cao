<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classes::all();
        $students = Student::all();

        foreach ($students as $student) {
            $selectedClasses = $classes->random(min(2, $classes->count()));

            foreach ($selectedClasses as $class) {
                Enrollment::query()->firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                    ],
                    [
                        'actual_price' => $class->base_price,
                        'enrolled_at' => now()->toDateString(),
                    ]
                );
            }
        }
    }
}
