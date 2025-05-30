<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class StatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->text(30)
        ];
    }
}
