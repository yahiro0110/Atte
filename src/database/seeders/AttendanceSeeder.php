<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use DateTime;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $targetDate = new DateTime('2023-10-01');

        for ($i = 0; $i < 100; $i++) {
            if ($targetDate->format('N') >= 6) {
                $targetDate->modify('next Monday');
            }

            Attendance::factory()->create([
                'date' => $targetDate->format('Y-m-d'),
            ]);

            $targetDate->modify('+1 day');
        }
    }
}
