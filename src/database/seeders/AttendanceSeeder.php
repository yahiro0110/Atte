<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use DateTime;

class AttendanceSeeder extends Seeder
{
    /**
     * 100日間の出勤・退勤データを生成する。
     *
     * このメソッドは、指定された従業員IDに対して、開始日から100日間の出勤・退勤データを生成する。
     * 週末（土曜日と日曜日）はスキップし、次の平日（月曜日）から再開する。
     *
     * @param int $employeeId 従業員のID
     * @return void このメソッドは戻り値を返さない
     */
    private function createDummyAttendanceData($employeeId)
    {
        // 開始日の指定
        $targetDate = new DateTime('2023-10-01');

        for ($i = 0; $i < 100; $i++) {
            if ($targetDate->format('N') >= 6) {
                $targetDate->modify('next Monday');
            }

            Attendance::factory()->create([
                'employee_id' => $employeeId,
                'date' => $targetDate->format('Y-m-d'),
            ]);

            $targetDate->modify('+1 day');
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($employeeId = 1; $employeeId <= 30; $employeeId++) {
            $this->createDummyAttendanceData($employeeId);
        }
    }
}
