<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * 特定のタイプの新規登録画面を表示する。
     *
     * タイプがmanagerの場合は、マネージャーのユーザ登録フォームを表示する。
     * タイプがmanager以外または指定されていない場合は、スタッフのユーザ登録フォームが表示される。
     *
     * @param string|null $type ユーザーのタイプ（manager: マネージャ、それ以外: スタッフ）
     *                    指定されていない場合はnull
     * @return \Illuminate\View\View ユーザー登録フォームのビュー
     */
    public function showCreateForm($type = null)
    {
        return view('auth.register', compact('type'));
    }

    /**
     * 新しい従業員を登録し、自動的にログインする。
     *
     * フォームリクエストから受け取ったデータを使用して、新しい従業員をデータベースに登録する。
     * 登録が完了すると、その従業員で自動的にログインし、ホームページにリダイレクトする。
     *
     * @param RegisterRequest $request ユーザー登録に関するリクエストデータ
     * @return \Illuminate\Http\RedirectResponse ホームページへのリダイレクトレスポンス
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

    /**
     * ログイン画面を表示する。
     *
     * タイプがmanagerの場合は、マネージャーのログインフォームを表示する。
     * タイプがmanager以外または指定されていない場合は、スタッフのログインフォームが表示される。
     *
     * @param string|null $type ユーザーのタイプ（manager: マネージャ、それ以外: スタッフ）
     *                    指定されていない場合はnull
     * @return \Illuminate\View\View ログインフォームのビュー
     */
    public function showLoginForm($type = null)
    {
        return view('auth.login', compact('type'));
    }

    /**
     * ユーザーのログイン処理をおこなう。
     *
     * リクエストからメールアドレスとパスワードを取得し、認証を試みる。
     * 認証に成功した場合はホーム画面にリダイレクトし、失敗した場合はエラーメッセージとともに前のページに戻る。
     *
     * @param LoginRequest $request ログインに関するリクエストデータ
     * @return \Illuminate\Http\RedirectResponse ログイン成功時はホーム画面へ、失敗時は前のページへのリダイレクトレスポンス
     */
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

    /**
     * ユーザーのログアウト処理を行い、ログアウト画面を表示する。
     *
     * 現在のユーザーをログアウトさせ、セッションを無効化し再生成する。
     * ログアウト後は、ユーザーのロールに基づいたログアウトビューが表示される。
     *
     * @return \Illuminate\View\View ログアウト後のビュー
     */
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
     * ホーム画面を表示する。
     *
     * @return \Illuminate\View\View ホーム画面のビュー
     */
    public function show()
    {
        return view('home');
    }

    /**
     * 従業員の打刻画面を表示する。
     *
     * このメソッドは、指定された従業員IDに基づいて従業員の勤怠情報を検索する。
     * 従業員が存在し、勤怠情報がある場合、その勤務状況をセッションに保存し、
     * 対応するビューにこれらの情報を渡す。
     *
     * @param int $employeeId 検索する従業員のID
     * @return \Illuminate\View\View 従業員の勤怠状況を表示するビュー
     */
    public function punch($employeeId)
    {
        // 従業員テーブル、出勤・退勤テーブルから該当従業員のレコードを取得
        $result = Employee::with('attendance')->find($employeeId);

        // 従業員の勤務状況を取得
        $workStatus = $result ? $result->getCurrentWorkStatus() : null;

        // 勤務状況をセッションに保存
        session()->flash('work_status', $workStatus);

        return view('punch', ['result' => $result]);
    }

    /**
     * スタッフ勤怠情報画面を表示する。
     *
     * このメソッドは、指定された日付に基づいて従業員の出勤・退勤レコードを表示する。
     * リクエストから年、月、日のクエリパラメータを取得し、それらを使用して出勤・退勤レコードを検索する。
     * 検索結果には休憩時間や勤務時間の計算も含まれ、最終的にスタッフのインデックスページに表示される。
     *
     * 補足：スタッフ勤怠情報にマネージャーの情報も含まれる。
     *
     * @param Request $request HTTPリクエストオブジェクト
     * @return \Illuminate\View\View 従業員の出勤・退勤レコードが含まれたビュー
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
}
