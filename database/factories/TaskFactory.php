<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Status;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(asText:true),
            'description' => fake()->text(),
            'status_id' => Status::factory()->create()->id,
            'priority' => fake()->randomElement([
                'Lowest',
                'Low',
                'Medium',
                'High',
                'Highest'
            ]),
            'reporter_id' => User::factory()->create()->id,
            'assignee_id' => User::factory()->create()->id,
            'due_date' => '2025-05-14',
        ];
    }
}
