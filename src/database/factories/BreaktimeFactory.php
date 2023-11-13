<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BreaktimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attendance_id' => $this->faker->numberBetween(1, 100),
            'start_time' => '',
            'end_time' => ''
        ];
    }
}
