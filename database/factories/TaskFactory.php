<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


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
        $user = User::factory()->create();
        return [
            'title' => fake()->words(asText:true),
            'description' => fake()->text(),
            'priority' => fake()->randomElement(TaskPriority::toArray()),
            'reporter_id' => $user->id,
            'assignee_id' => $user->id,
            'due_date' => fake()->dateTimeBetween('+0 days', '+30 days')->format('Y-m-d'),
        ];
    }
}
