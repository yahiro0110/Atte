<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputTimeRequest;
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
     * @param int $employeeId 従業員レコードのID
     * @return \Illuminate\View\View 勤怠情報を表示するビュー
     */
    public function index(Request $request, $employeeId)
    {
        // リクエストのクエリ情報から出勤・退勤レコード検索用の年月を取得
        $setYear = $request->has('year') ? $request->input('year') : now()->year;
        $setMonth = $request->has('month') ? $request->input('month') : now()->month;

        // 出勤・退勤テーブル、休憩テーブルから該当月のレコードを取得
        $results = Attendance::forEmployeeAndMonth($employeeId, $setYear, $setMonth)->get();

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
     * 特定の勤怠レコードを表示する。
     *
     * このメソッドは、指定された出勤・退勤IDに基づいて勤怠レコードを取得する。
     * 出勤・退勤IDが0の場合は、新しい勤怠レコードのオブジェクトをデフォルト値で初期化する。
     * そうでない場合は、指定されたIDに一致する出勤・退勤レコードとそれに関連する休憩レコードを取得する。
     * 最終的に、これらの情報を含む日付編集のビューを返す。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $attendanceId 表示する出勤・退勤レコードのID（新規の場合は0）
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
     * 指定された出勤・退勤レコードとその休憩レコードを更新する。
     *
     * このメソッドは、指定された出勤・退勤IDに基づいて出勤・退勤レコードを更新する。
     * さらに、リクエストに休憩時間のIDが含まれている場合は、関連する休憩レコードも更新する。
     * 処理完了後、ユーザーは日付一覧画面にリダイレクトされ、更新が完了したことを示すメッセージが表示される。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $attendanceId 更新する出勤・退勤レコードのID
     * @return \Illuminate\Http\RedirectResponse 勤怠一覧ページへのリダイレクト
     */
    public function update(InputTimeRequest $request, $attendanceId)
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
        $data = $request->only(['start_time', 'end_time']);
        $data['work_status'] = Attendance::setWorkStatus($data['start_time'], $data['end_time']);

        $attendance = Attendance::updateOrCreate(
            ['id' => $attendanceId],
            $data + [
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
     * @param int $attendanceId 関連する出勤・退勤レコードのID
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
     * 指定された従業員の出勤・退勤時刻と休憩時刻を打刻する。
     *
     * このメソッドは、従業員の出勤・退勤および休憩の打刻を行う。
     * 打刻タイプに応じて、対応する処理（出勤、退勤、休憩開始、休憩終了）が実行される。
     * 完了後、打刻の結果と現在の勤務状態がJSONレスポンスとして返される。
     *
     * @param \Illuminate\Http\Request $request HTTPリクエストインスタンス
     * @param int $employeeId 従業員ID
     * @return \Illuminate\Http\JsonResponse 打刻処理の結果
     */
    public function punch(Request $request, $employeeId)
    {
        // 今日の日付かつ指定の従業員IDを持つレコードを検索
        $result = Attendance::with('breaktimes')
            ->where('date', now()->format('Y-m-d'))
            ->where('employee_id', $employeeId)
            ->first();

        // 打刻処理
        switch ($request->input('punch_type')) {
                // 出勤処理
            case 'clockIn':
                if ($result) {
                    $message = [
                        'content' => 'すでに出勤しています',
                        'type' => 'error'
                    ];
                } else {
                    $message = [
                        'content' => $this->addClockInTime($employeeId),
                        'type' => 'success'
                    ];
                }
                break;
                // 退勤処理
            case 'clockOut':
                if ($result) {
                    $message = [
                        'content' =>  $this->addClockOutTime($result),
                        'type' => 'success'
                    ];
                } else {
                    $message = [
                        'content' => '出勤情報がありません',
                        'type' => 'error'
                    ];
                }
                break;
                // 休憩開始処理
            case 'onBreak':
                if ($result) {
                    $message = [
                        'content' =>  $this->addOnBreakTime($result),
                        'type' => 'success'
                    ];
                } else {
                    $message = [
                        'content' => '出勤情報がありません',
                        'type' => 'error'
                    ];
                }
                break;
                // 休憩終了処理
            case 'offBreak':
                if ($result) {
                    $message = [
                        'content' =>  $this->addOffBreakTime($result),
                        'type' => 'success'
                    ];
                } else {
                    $message = [
                        'content' => '出勤情報がありません',
                        'type' => 'error'
                    ];
                }
                break;
                // 例外処理
            default:
                # code...
                break;
        }

        // 勤務状態を設定
        $workStatus = Attendance::WORK_STATUSES[$request->input('punch_type')];
        $employeeName = $request->input('employee_name');

        return response()->json(['message' => $message, 'employee_name' => $employeeName, 'work_status' => $workStatus]);
    }

    /**
     * 従業員の出勤時刻を登録する。
     *
     * このメソッドは、指定された従業員IDに対して現在の日付と時刻を使用して出勤記録を作成する。
     * 出勤時刻として現在時刻が登録され、作業状態は「出勤中」に設定される。
     *
     * @param int $employeeId 従業員のID
     * @return string 登録完了メッセージ
     */
    private function addClockInTime($employeeId)
    {
        Attendance::create([
            'employee_id' => $employeeId,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->format('H:i'),
            'work_status' => Attendance::WORK_STATUSES['clockIn']
        ]);

        return '出勤時刻を登録しました';
    }

    /**
     * 従業員の退勤時刻を登録する。
     *
     * このメソッドは、指定された出勤・退勤レコードに退勤時刻を更新する。
     * 退勤時刻として現在時刻が登録され、作業状態は「退勤済み」に設定される。
     *
     * @param \App\Models\Attendance $result 出勤・退勤レコード
     * @return string 登録完了メッセージ
     */
    private function addClockOutTime($result)
    {
        $result->update([
            'end_time' => now()->format('H:i'),
            'work_status' => Attendance::WORK_STATUSES['clockOut']
        ]);

        return '退勤時刻を登録しました';
    }

    /**
     * 休憩開始時刻を登録する。
     *
     * このメソッドは、指定された出勤・退勤レコードに休憩開始時刻を登録する。
     * 休憩開始時刻として現在時刻が登録され、作業状態は「休憩中」に設定される。
     *
     * @param \App\Models\Attendance $result 出勤・退勤レコード
     * @return string 登録完了メッセージ
     */
    private function addOnBreakTime($result)
    {
        Breaktime::create([
            'attendance_id' => $result->id,
            'start_time' => now()->format('H:i'),
        ]);

        $result->update([
            'work_status' => Attendance::WORK_STATUSES['onBreak']
        ]);

        return '休憩開始時刻を登録しました';
    }

    /**
     * 休憩終了時刻を登録する。
     *
     * このメソッドは、最新の休憩レコードに休憩終了時刻を登録する。
     * 休憩終了時刻として現在時刻が登録され、出勤・退勤レコードの作業状態は「休憩終了」に設定される。
     *
     * @param \App\Models\Attendance $result 出勤・退勤レコード
     * @return string 登録完了メッセージ
     */
    private function addOffBreakTime($result)
    {
        $breaktime = $result->breaktimes->last();
        $breaktime->update([
            'end_time' => now()->format('H:i'),
        ]);

        $result->update([
            'work_status' => Attendance::WORK_STATUSES['offBreak']
        ]);

        return '休憩終了時刻を登録しました';
    }
}
