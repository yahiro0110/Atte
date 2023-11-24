<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Breaktime;
use DateTime;
use DateInterval;

class BreaktimeSeeder extends Seeder
{
    /**
     * 指定した時間帯内でランダムな時刻を生成する。
     *
     * @param int $startHour 開始時間（時）
     * @param int $endHour   終了時間（時）
     *
     * @return DateTime 生成された時刻を表す DateTime オブジェクト。
     */
    private function generateRandomTime($startHour, $endHour)
    {
        $hour = rand($startHour, $endHour);
        $minute = rand(0, 59);
        $second = rand(0, 59);

        $time = new DateTime();
        $time->setTime($hour, $minute, $second);

        return $time;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 1500; $i++) {
            // 8時から15時の間でランダムの時刻を生成
            $startTime = $this->generateRandomTime(8, 15);

            // Breaktimeモデルを使ってダミーデータを生成
            Breaktime::factory()->create([
                'start_time' => $startTime->format('H:i'),
                'end_time' => $startTime->add(new DateInterval('PT15M'))->format('H:i')
            ]);
        }
    }
}
