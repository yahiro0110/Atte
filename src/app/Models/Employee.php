<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Authenticatable インターフェイスとトレイトを追加
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

/**
 * 従業員情報を表すモデル。
 *
 * 従業員の基本情報を保持し、関連する勤怠情報や役割の判定などの機能を提供する。
 */
class Employee extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $fillable = ['name', 'email', 'role', 'password'];

    /**
     * 指定されたロールを持っているかどうかを判定する。
     *
     * @param string $role 確認したいロール（1:マネージャ、2:スタッフ）
     * @return bool 指定したロールを持っている場合はtrue、そうでない場合はfalse
     */
    public function hasRole($role)
    {
        return $role === 'admin' && $this->role === 1;
    }

    /**
     * この従業員に関連する出勤記録を取得する。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 出勤記録のリレーション
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * この従業員の現在の出勤記録を取得する。
     * 補足：打刻画面で使用する。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne 現在の出勤記録のリレーション
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class)
            ->where('date', now()->format('Y-m-d'));
    }

    /**
     * 従業員の現在の勤務状態を取得する。
     *
     * @return string|null 勤務状態
     */
    public function getCurrentWorkStatus()
    {
        if ($this->attendance) {
            switch ($this->attendance->work_status) {
                case '1':
                    return Attendance::WORK_STATUSES['clockIn'];

                case '2':
                    return Attendance::WORK_STATUSES['onBreak'];

                case '3':
                    return Attendance::WORK_STATUSES['offBreak'];

                case '4':
                    return Attendance::WORK_STATUSES['clockOut'];

                case '5':
                    return Attendance::WORK_STATUSES['noClockOut'];

                case '6':
                    return Attendance::WORK_STATUSES['noWork'];

                default:
                    // エラーハンドリング用に何の値が入っているかを確認する
                    return $this->attendance->work_status;
            }
        }
        return null;
    }

    /**
     * 指定された日付の全従業員の出勤記録を取得する。
     *
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @return \Illuminate\Pagination\LengthAwarePaginator 出勤記録のページネーションされた結果
     */
    public static function getAttendancesForDate($year, $month, $day)
    {
        return self::with([
            'attendances' => function ($query) use ($year, $month, $day) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->whereDay('date', $day)
                    ->with('breaktimes');
            }
        ])
            // *REF: 権限で絞り込みたい場合はここでwhereを追加する
            // * ->where('role', 2)
            ->paginate(5);
    }

    /**
     * 提供された従業員の出勤記録に対して、勤務時間と休憩時間を計算する。
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $employees 従業員の出勤記録のページネーションされたコレクション
     * @return \Illuminate\Pagination\LengthAwarePaginator 計算された勤務時間と休憩時間が含まれる、従業員の出勤記録のコレクション
     */
    public static function calculateAttendanceData($employees)
    {
        foreach ($employees as $employee) {
            foreach ($employee->attendances as $attendance) {
                $attendance->total_break_time = $attendance->calculateTotalBreakTimes($attendance->id, $attendance->breaktimes);
                $attendance->total_work_time = $attendance->calculateTotalWorkTime($attendance->start_time, $attendance->end_time, $attendance->total_break_time);
            }
        }

        return $employees;
    }
}
