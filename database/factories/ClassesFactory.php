<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classes>
 */
class ClassesFactory extends Factory
{
    protected $model = Classes::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+3 months');

        return [
            'name' => 'Class '.fake()->bothify('??-###'),
            'subject_id' => Subject::query()->inRandomOrder()->value('id') ?? Subject::factory(),
            'teacher_id' => Teacher::query()->inRandomOrder()->value('id') ?? Teacher::factory(),
            'base_price' => fake()->numberBetween(1200000, 3500000),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'active',
        ];
    }
}
