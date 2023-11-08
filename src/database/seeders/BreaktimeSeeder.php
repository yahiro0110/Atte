<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Breaktime;
use DateTime;

class BreaktimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 200; $i++) {
            // ランダムな時間のコンポーネントを生成
            $hour = rand(8, 15);    // 0から23までのランダムな時間（時）
            $minute = rand(0, 59);  // 0から59までのランダムな時間（分）
            $second = rand(0, 59);  // 0から59までのランダムな時間（秒）

            // ランダムな時間でDateTimeオブジェクトを作成
            $startTime = new DateTime();
            $startTime->setTime($hour, $minute, $second);

            Breaktime::factory()->create([
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $startTime->modify('+15 minutes')->format('H:i:s')
            ]);
        }
    }
}
