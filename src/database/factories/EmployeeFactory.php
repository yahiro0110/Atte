<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' =>  $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'role' => $this->faker->numberBetween(1, 2),
            'password' => $this->faker->password
        ];
    }
}
