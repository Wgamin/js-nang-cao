<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Mathematics',
            'Literature',
            'English',
            'Physics',
            'Chemistry',
        ];

        foreach ($subjects as $name) {
            Subject::query()->firstOrCreate(['name' => $name]);
        }
    }
}
