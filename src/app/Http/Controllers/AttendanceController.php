<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Breaktime;

class AttendanceController extends Controller
{
    /**
     * 指定された従業員の特定の年月の勤怠情報を表示する。
     *
     * このメソッドは、指定された従業員IDに対応する勤怠情報を取得し、
     * それぞれの日に対する休憩時間と勤務時間を計算する。
     * もしリクエストに年月が含まれていない場合は、現在の年月が使用される。
     * 最終的に、これらの情報を含む日付一覧のビューを返す。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $employeeId 従業員ID
     * @return \Illuminate\View\View 勤怠情報を表示するビュー
     */
    public function index(Request $request, $employeeId)
    {
        // リクエストのクエリ情報からAttendancesレコード検索用の年月を取得
        $setYear = $request->has('year') ? $request->input('year') : now()->year;
        $setMonth = $request->has('month') ? $request->input('month') : now()->month;

        // Attendancesテーブル、Breaktimesテーブルから該当月のレコードを取得
        $results = Attendance::with('breaktimes')
            ->where('employee_id', $employeeId)
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
        $results = Attendance::getAllDateRecords($results, $setYear, $setMonth, $employeeId);

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
     * 特定の勤怠レコードを表示する。
     *
     * このメソッドは、指定された勤怠IDに基づいて勤怠レコードを取得する。
     * 勤怠IDが0の場合は、新しい勤怠レコードのオブジェクトをデフォルト値で初期化する。
     * そうでない場合は、指定されたIDに一致する勤怠レコードとそれに関連する休憩時間を取得する。
     * 最終的に、これらの情報を含む日付編集のビューを返す。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $attendanceId 表示する勤怠情報のID（新規の場合は0）
     * @return \Illuminate\View\View 勤怠情報を編集するビュー
     */
    public function show(Request $request, $attendanceId)
    {
        if ($attendanceId == 0) {
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
            $results = Attendance::with('breaktimes')->where('id', $attendanceId)->first();
            $breakTime = $results->calculateTotalBreakTimes($attendanceId, $results->breaktimes);
        }

        return view('edit', compact('attendanceId', 'results', 'breakTime'));
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
     * 指定された出勤・退勤レコードとその休憩レコードを更新する。
     *
     * このメソッドは、指定された勤怠IDに基づいて出勤・退勤レコードを更新する。
     * さらに、リクエストに休憩時間のIDが含まれている場合は、関連する休憩レコードも更新する。
     * 処理完了後、ユーザーは日付一覧画面にリダイレクトされ、更新が完了したことを示すメッセージが表示される。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $attendanceId 更新する勤怠情報のID
     * @return \Illuminate\Http\RedirectResponse 勤怠一覧ページへのリダイレクト
     */
    public function update(Request $request, $attendanceId)
    {
        // 出勤・退勤レコードの更新
        $attendance = $this->updateAttendance($request, $attendanceId);

        // 休憩レコードの更新
        if ($request->has('breaktime_ids')) {
            $this->updateBreaktime($request, $attendance->id);
        }

        return redirect()->route('attendance.index', ['id' => $request->input('employee_id')])->with('message', $request->date . 'の勤怠情報を更新しました');
    }

    /**
     * 出勤・退勤レコードの更新または作成する。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $attendanceId 出勤・退勤レコードのID（新規作成の場合は0）
     * @return \App\Models\Attendance 更新または作成された出勤・退勤レコード
     */
    private function updateAttendance($request, $attendanceId)
    {
        $date = $request->only(['start_time', 'end_time']);
        $date['work_status'] = Attendance::setWorkStatus($date['start_time'], $date['end_time']);

        $attendance = Attendance::updateOrCreate(
            ['id' => $attendanceId],
            $date + [
                'employee_id' => $request->input('employee_id'),
                'date' => $request->input(['date'])
            ]
        );

        return $attendance;
    }

    /**
     * 休憩レコードを更新または作成する。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $id 関連する出勤・退勤レコードのID
     */
    private function updateBreaktime($request, $attendanceId)
    {
        $breaktimeIds = $request->input('breaktime_ids');
        $startTimes = $request->input('breaktime_start_time');
        $endTimes = $request->input('breaktime_end_time');

        for ($i = 0; $i < count($breaktimeIds); $i++) {
            Breaktime::updateOrCreate(
                ['id' => $breaktimeIds[$i]],
                [
                    'attendance_id' => $attendanceId,
                    'start_time' => $startTimes[$i],
                    'end_time' => $endTimes[$i],
                ]
            );
        }
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
