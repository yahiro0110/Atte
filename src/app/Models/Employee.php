<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'role', 'password'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class)
            ->where('date', now()->format('Y-m-d'));
    }

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
            ->where('role', 2)
            ->paginate(5);
    }

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
