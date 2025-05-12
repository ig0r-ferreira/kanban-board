<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class StatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->text(30)
        ];
    }

    public function withNameLengthGreaterThan30(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->sentences(4, true)
        ]);
    }
}
