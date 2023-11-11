<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Breaktime;

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

        // 月初から月末までの勤怠レコードを作成
        $results = Attendance::getAllDateRecords($results, $setYear, $setMonth, $id);

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
    public function show($id, Request $request)
    {
        if ($id == 0) {
            $results = new Attendance(
                [
                    'employee_id' => $request->input('employee_id'),
                    'date' => $request->input('date'),
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'work_status' => $request->input('work_status'),
                ]
            );
            $breakTime = '00:00:00';
        } else {
            $results = Attendance::with('breaktimes')->where('id', $id)->first();
            $breakTime = $results->calculateTotalBreakTimes($id, $results->breaktimes);
        }

        return view('edit', compact('id', 'results', 'breakTime'));
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
    public function update(Request $request, $id)
    {
        // 出勤・退勤レコードの更新
        $attendance = Attendance::findOrFail($id);
        $attendance->start_time = $request->input('start_time');
        $attendance->end_time = $request->input('end_time');
        $attendance->save();

        // 休憩レコードの更新
        $breaktimeIds = $request->input('breaktime_ids');
        $startTimes = $request->input('breaktime_start_time');
        $endTimes = $request->input('breaktime_end_time');
        for ($i = 0; $i < count($breaktimeIds); $i++) {
            // 休憩レコードが存在しない場合は新規作成
            if ($breaktimeIds[$i] == 0) {
                Breaktime::create(
                    [
                        'attendance_id' => $id,
                        'start_time' => $startTimes[$i],
                        'end_time' => $endTimes[$i],
                    ]
                );
            } else {
                $breaktime = Breaktime::findOrFail($breaktimeIds[$i]);
                $breaktime->start_time = $startTimes[$i];
                $breaktime->end_time = $endTimes[$i];
                $breaktime->save();
            }
        }

        return redirect()->route('attendance.index', ['id' => $request->input('employee_id')])->with('message', '勤怠情報を更新しました。');
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
