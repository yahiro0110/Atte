<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm($type = null)
    {
        return view('auth.register', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $user = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function showLoginForm($type = null)
    {
        return view('auth.login', compact('type'));
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect(RouteServiceProvider::HOME);
        }

        // 認証に失敗した場合
        return back()->withErrors([
            'error' => 'メールアドレスまたはパスワードが違います',
        ]);
    }

    public function logout()
    {
        $role = Auth::user()->role;
        Auth::logout();

        // セッションを無効化する
        request()->session()->invalidate();

        // セッションの再生成
        request()->session()->regenerateToken();

        return view('auth.logout', compact('role'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // リクエストのクエリ情報から出勤・退勤レコード検索用の日付を取得
        $setYear = $request->has('year') ? $request->input('year') : now()->year;
        $setMonth = $request->has('month') ? $request->input('month') : now()->month;
        $setDay = $request->has('day') ? $request->input('day') : now()->day;

        // 従業員テーブル、出勤・退勤テーブル、休憩テーブルから該当日付のレコードを取得
        $results = Employee::getAttendancesForDate($setYear, $setMonth, $setDay);

        // 休憩時間、勤務時間を計算
        $results = Employee::calculateAttendanceData($results);

        // クエリパラメータをページネーションリンクに含める
        $results->appends($request->all());

        return view('staff_index', compact('results'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('home');
    }

    public function punch($employeeId)
    {
        $result = Employee::with('attendance')->find($employeeId);

        if ($result && $result->attendance) {
            // TODO: switch文を使用しなくとも、$result->attendance->work_statusをそのままビューに渡せばよい
            switch ($result->attendance->work_status) {
                case '1':
                    $workStatus = Attendance::WORK_STATUSES['clockIn'];
                    break;

                case '2':
                    $workStatus = Attendance::WORK_STATUSES['onBreak'];
                    break;

                case '3':
                    $workStatus = Attendance::WORK_STATUSES['offBreak'];
                    break;

                case '4':
                    $workStatus = Attendance::WORK_STATUSES['clockOut'];
                    break;

                case '5':
                    $workStatus = Attendance::WORK_STATUSES['noClockOut'];
                    break;

                case '6':
                    $workStatus = Attendance::WORK_STATUSES['noWork'];
                    break;

                default:
                    $workStatus = 0;
                    break;
            }
        } else {
            $workStatus = null;
        }

        session()->flash('work_status', $workStatus);

        return view('punch', ['result' => $result]);
    }
}
