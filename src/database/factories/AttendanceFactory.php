<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => 1,
            'date' => '',
            'start_time' => date('H:i:s', mt_rand(strtotime('2023-11-15 08:00:00'), strtotime('2023-11-15 12:00:00'))),
            'end_time' => date('H:i:s', mt_rand(strtotime('2023-11-15 17:00:00'), strtotime('2023-11-15 23:00:00'))),
            'work_status' => 4
        ];
    }
}
