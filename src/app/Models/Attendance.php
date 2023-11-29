<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Carbon\Carbon;

/**
 * 勤怠情報を表すモデル。
 *
 * 従業員の勤怠情報を保持し、関連する勤怠情報の計算や勤務状況の判定などの機能を提供する。
 */
class Attendance extends Model
{
    use HasFactory;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * 従業員の勤務状態を表す定数。
     *
     * 各状態は数値で表され、勤怠管理において以下の異なる状況を識別するために使用される：
     * - `clockIn` : 出勤
     * - `onBreak` : 休憩中
     * - `offBreak` : 休憩終了
     * - `clockOut` : 退勤
     * - `noClockOut` : 退勤打刻なし
     * - `noWork` : 勤務なし
     */
    const WORK_STATUSES = [
        'clockIn' => 1,
        'onBreak' => 2,
        'offBreak' => 3,
        'clockOut' => 4,
        'noClockOut' => 5,
        'noWork' => 6
    ];

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `employee_id` : 従業員ID
     * - `date` : 日付
     * - `start_time` : 出勤時刻
     * - `end_time` : 退勤時刻
     * - `work_status` : 勤務状態
     *
     * @var array
     */
    protected $fillable = ['employee_id', 'date', 'start_time', 'end_time', 'work_status'];

    /**
     * モデルのJSON表現に追加されるアクセサ属性。
     *
     * このプロパティは、モデルがJSONに変換される際に、モデルの標準の属性に加えて含めるべき追加の属性を指定する。
     * - `total_break_time` : 休憩時間の合計
     * - `total_work_time` : 勤務時間の合計
     *
     * @var array
     */
    protected $append = ['total_break_time', 'total_work_time'];

    /**
     * この勤怠レコードに関連付けられている従業員エンティティを取得するリレーションシップ。
     *
     * `Employee` モデルとの "BelongsTo" 関連を定義する。
     * これにより、特定の勤怠レコードに関連付けられている従業員の情報にアクセスできるようになる。
     * （例えば、勤怠レコードから直接関連する従業員の名前や他の属性にアクセスする場合に使用）
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 勤怠レコードに関連する従業員モデルへのリレーションシップ
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * この勤怠レコードに関連付けられている全ての休憩時間を取得するリレーションシップ。
     *
     * `Breaktime` モデルとの "HasMany" 関連を定義する。
     * これにより、特定の勤怠レコードに関連する複数の休憩時間のレコードにアクセスできるようになる。
     * （例えば、勤怠レコードに紐づく全ての休憩時間を取得したい場合などに使用）
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 勤怠レコードに関連する休憩時間のモデルのコレクションへのリレーションシップ
     */
    public function breaktimes()
    {
        return $this->hasMany(Breaktime::class);
    }

    // --------------------------------------------------------------------------------
    // クエリスコープとカスタムメソッド
    // --------------------------------------------------------------------------------

    /**
     * 指定された従業員ID、年、月に基づいて勤怠レコードを取得するクエリスコープ。
     *
     * このスコープは `Attendance` モデルに対して、特定の従業員ID、年、月に該当する
     * 勤怠レコードを絞り込むために使用される。このメソッドを通じて、効率的に
     * 必要なレコードを取得し、関連する休憩時間のレコードも同時にEager Loadingする。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query 現在のクエリビルダーインスタンス
     * @param int $employeeId 取得したい勤怠レコードの従業員ID
     * @param int $year 取得したい勤怠レコードの年
     * @param int $month 取得したい勤怠レコードの月
     * @return \Illuminate\Database\Eloquent\Builder 更新されたクエリビルダーインスタンス
     */
    public function scopeForEmployeeAndMonth($query, $employeeId, $year, $month)
    {
        return $query->with('breaktimes')
            ->where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    /**
     * 出勤・退勤IDに関連する全ての休憩時間の合計を計算する。
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
     * 二つのDateIntervalオブジェクトを合計する。
     *
     * @param DateInterval $interval1 合計休憩時間
     * @param DateInterval $interval2 追加用の休憩時時間
     * @return DateInterval 二つの間隔の合計を示すDateIntervalオブジェクト
     */
    private function addDateIntervals($interval1, $interval2)
    {
        $e = new DateTimeImmutable();
        $total = $e->add($interval1)->add($interval2);
        return $e->diff($total);
    }

    /**
     * 指定された出勤開始時刻、出勤終了時刻、休憩時間をもとに勤務時間を計算する。
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
     * 二つの時刻間の時間差をDateIntervalオブジェクトとして返す。
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
     * DateIntervalオブジェクトを秒単位で表現する。
     *
     * @param DateInterval $interval DateIntervalオブジェクト
     * @return int インターバルを秒単位で表した値
     */
    private function calculateDiffInSeconds($interval)
    {
        return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }

    // --------------------------------------------------------------------------------
    // 静的ヘルパーメソッド
    // --------------------------------------------------------------------------------

    /**
     * 指定された年と月に基づいて、勤怠レコードのコレクションを生成する。
     * 既存のレコードがない日付には、デフォルト値を持つ新しいレコードが作成される。
     *
     * @param \Illuminate\Support\Collection $records 既に取得されている勤怠レコードのコレクション
     * @param int $targetYear 対象の年
     * @param int $targetMonth 対象の月
     * @param int $employeeId 対象の従業員ID
     * @return \Illuminate\Support\Collection 日付順にソートされた勤怠レコード（全日付分）のコレクション
     */
    public static function getAllDateRecords($records, $targetYear, $targetMonth, $employeeId)
    {
        $startDate = Carbon::create($targetYear, $targetMonth, 1)->format('Y-m-d');
        $endDate = Carbon::create($targetYear, $targetMonth, 1)->endOfMonth()->format('Y-m-d');

        // 月初から月末までの日付オブジェクトを取得（検索で使用）
        $period = Carbon::parse($startDate)->daysUntil($endDate);

        // 月初から月末までの勤怠レコードを作成（ビューで使用）
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            if (!$records->contains('date', $dateString)) {
                $records->push(new Attendance([
                    'employee_id' => $employeeId,
                    'date' => $dateString,
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'work_status' => self::WORK_STATUSES['noWork']
                ]));
            }
        }

        // 日付順にソート
        $records = $records->sortBy('date');

        return $records;
    }

    /**
     * 指定された勤務開始時刻と終了時刻に基づいて勤務状態を決定する。
     * 勤務なし、退勤、出勤の3つの状態がある。
     *
     * @param string $startTime 勤務の開始時刻（形式：'HH:MM:SS'）
     * @param string $endTime   勤務の終了時刻（形式：'HH:MM:SS'）
     *
     * @return int 勤務状態を表す数値
     *             勤務なしの場合は6、退勤の場合は4、出勤の場合は1を返す
     */
    public static function setWorkStatus($startTime, $endTime)
    {
        // 勤務なしの場合
        if ($startTime === '00:00:00' && $endTime === '00:00:00') {
            return self::WORK_STATUSES['noWork'];
        }
        // 退勤の場合
        if ($startTime !== '00:00:00' && $endTime !== '00:00:00') {
            return self::WORK_STATUSES['clockOut'];
        }
        // 出勤の場合
        if ($startTime !== '00:00:00' && $endTime === '00:00:00') {
            return self::WORK_STATUSES['clockIn'];
        }
    }
}
