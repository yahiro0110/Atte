<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateInterval;
use DateTime;
use DateTimeImmutable;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'start_time', 'end_time', 'work_status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function breaktimes()
    {
        return $this->hasMany(Breaktime::class);
    }

    /**
     * 出勤・退勤IDに関連する全ての休憩時間の合計を計算する
     *
     * @param int $attendanceId 出勤・退勤ID
     * @param Illuminate\Database\Eloquent\Collection $breaktimes 出勤・退勤IDに紐づく休憩時間レコードの集合
     * @return string 合計休憩時間をHH:MM:SS形式の文字列として返す
     */
    public function calculateTotalBreakTimes($attendanceId, $breaktimes)
    {
        $totalBreakTime = new DateInterval('PT0S');

        foreach ($breaktimes as $record) {
            if ($record->attendance_id == $attendanceId) {
                $startTime = new DateTime($record->start_time);
                $endTime = new DateTime($record->end_time);
                $breakTime = $startTime->diff($endTime);
                $totalBreakTime = $this->addDateIntervals($totalBreakTime, $breakTime);
            }
        }

        return $totalBreakTime->format('%H:%I:%S');
    }

    /**
     * 二つのDateIntervalオブジェクトを合計する
     *
     * @param DateInterval $interval1 最初の時間間隔
     * @param DateInterval $interval2 二番目の時間間隔
     * @return DateInterval 二つの間隔の合計を示すDateIntervalオブジェクト
     */
    private function addDateIntervals($interval1, $interval2)
    {
        $e = new DateTimeImmutable();
        $total = $e->add($interval1)->add($interval2);
        return $e->diff($total);
    }

    /**
     * 指定された出勤開始時刻、出勤終了時刻、休憩時間をもとに勤務時間を計算する
     *
     * @param string $startTime 勤務開始時刻（'HH:MM:SS'形式）
     * @param string $endTime 勤務終了時刻（'HH:MM:SS'形式）
     * @param string $breakTimeStr 休憩時間（'HH:MM:SS'形式）
     * @return string 勤務時間を'HH:MM:SS'形式の文字列で返す
     */
    public function calculateTotalWorkTime($startTime, $endTime, $breakTimeStr)
    {
        // 勤務時間を計算（DateInterval オブジェクト）
        $workTimeInterval = $this->workTimeInterval($startTime, $endTime);

        // 休憩時間を DateInterval に変換
        $breakTime = new DateInterval('PT' . str_replace(':', 'H', substr($breakTimeStr, 0, -3)) . 'M' . substr($breakTimeStr, -2) . 'S');

        // 勤務時間から休憩時間を差し引く
        $actualWorkTimeInSeconds = $this->calculateDiffInSeconds($workTimeInterval) - $this->calculateDiffInSeconds($breakTime);

        // 結果を HH:MM:SS 形式に変換
        return gmdate('H:i:s', $actualWorkTimeInSeconds);
    }

    /**
     * 二つの時刻間の時間差をDateIntervalオブジェクトとして返す
     *
     * @param string $startTime 開始時刻（'HH:MM:SS'形式）
     * @param string $endTime 終了時刻（'HH:MM:SS'形式）
     * @return DateInterval 開始時刻と終了時刻の差を表すDateIntervalオブジェクト
     */
    private function workTimeInterval($startTime, $endTime)
    {
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);
        return $start->diff($end);
    }

    /**
     * DateIntervalオブジェクトを秒単位で表現する
     *
     * @param DateInterval $interval DateIntervalオブジェクト
     * @return int インターバルを秒単位で表した値
     */
    private function calculateDiffInSeconds($interval)
    {
        return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }
}
