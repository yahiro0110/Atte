<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        // リクエストのクエリ情報からAttendancesレコード検索用の年月を取得
        $setYear = $request->has('year') ? $request->input('year') : now()->year;
        $setMonth = $request->has('month') ? $request->input('month') : now()->month;

        // Attendancesテーブル、Breaktimesテーブルから該当月のレコードを取得
        $results = Attendance::with('breaktimes')
            ->where('employee_id', $id)
            ->whereYear('date', $setYear)
            ->whereMonth('date', $setMonth)
            ->get();

        // 休憩時間、勤務時間を計算
        $breakTimes = [];
        $workedTimes = [];

        foreach ($results as $attendance) {
            $breakTime = $attendance->calculateTotalBreakTimes($attendance->id, $attendance->breaktimes);
            $workedTime = $attendance->calculateTotalWorkTime($attendance->start_time, $attendance->end_time, $breakTime);
            $breakTimes[$attendance->id] = $breakTime;
            $workedTimes[$attendance->id] = $workedTime;
        }

        // 当該月の勤怠レコード（月初から月末分）を作成
        $startDate = Carbon::create($setYear, $setMonth, 1)->format('Y-m-d');
        $endDate = Carbon::create($setYear, $setMonth, 1)->endOfMonth()->format('Y-m-d');

        // 月初から月末までの日付オブジェクトを取得（検索で使用）
        $period = Carbon::parse($startDate)->daysUntil($endDate);

        // 月初から月末までの勤怠レコードを作成（ビューで使用）
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            if (!$results->contains('date', $dateString)) {
                $results->push(new Attendance([
                    'employee_id' => 0,
                    'date' => $dateString,
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'work_status' => 6
                ]));
            }
        }

        // 日付順にソート
        $results = $results->sortBy('date');

        return view('index', compact('results', 'breakTimes', 'workedTimes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Unused variable $request removed
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Unused variables $request and $attendance removed
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        // Unused variable $attendance removed
        //
    }
}
