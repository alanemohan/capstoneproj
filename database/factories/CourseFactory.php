<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => \App\Models\User::factory()->teacher(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'price' => 0,
            'status' => 'published',
            'subject' => $this->faker->word(),
        ];
    }
}
