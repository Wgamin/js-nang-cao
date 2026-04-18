<?php

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'guardian_id' => Guardian::query()->inRandomOrder()->value('id') ?? Guardian::factory(),
            'name' => fake()->name(),
            'dob' => fake()->dateTimeBetween('-18 years', '-8 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
        ];
    }
}
