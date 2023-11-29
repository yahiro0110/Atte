<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreaktimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 新規登録画面
Route::get('/register/{type?}', [EmployeeController::class, 'create'])->name('employee.create');
Route::post('/register', [EmployeeController::class, 'store'])->name('employee.store');

// ログイン画面
Route::get('/login/{type?}', [EmployeeController::class, 'showLoginForm'])->name('employee.loginForm');
Route::post('/login', [EmployeeController::class, 'login'])->name('employee.login');

// ログアウト画面
Route::get('/logout', [EmployeeController::class, 'logout'])->name('employee.logout');

// 警告画面（認証システムを通過せずに所定の画面をアクセスすると表示）
Route::get('/warning', function () {
    return view('warning');
})->name('warning');

Route::middleware(['custom_auth'])->group(function () {
    // ホーム画面
    Route::get('/', [EmployeeController::class, 'show'])->name('employee.home');
    // 打刻画面
    Route::get('/punch/{id}', [EmployeeController::class, 'punch'])->name('employee.punch');
    Route::post('/punch/{id}', [AttendanceController::class, 'punch'])->name('attendance.punch');
    // 日付一覧画面
    Route::get('/attendance/{id}', [AttendanceController::class, 'index'])->name('attendance.index');
    // 日付編集画面
    Route::get('/edit/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/edit/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/breaktime/delete/{id}', [BreaktimeController::class, 'destroy'])->name('breaktime.destroy');
    // スタッフ勤怠情報画面
    Route::get('/staff/attendance/', [EmployeeController::class, 'index'])->name('employee.index');
});

// 警告画面（アクセス権限のない画面にアクセスすると表示）
Route::get('/caution', function () {
    return view('caution');
})->name('caution');

Route::middleware(['custom_role:admin'])->group(function () {
    // スタッフ勤怠情報画面
    Route::get('/staff/attendance/', [EmployeeController::class, 'index'])->name('employee.index');
});
