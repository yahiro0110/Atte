<?php

use App\Http\Controllers\Auth\RegisterController;
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

Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/', [EmployeeController::class, 'show'])->name('employee.home');
Route::get('/punch/{id}', [EmployeeController::class, 'punch'])->name('employee.punch');

Route::get('/attendance/{id}', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/edit/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::post('/edit/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
Route::post('/punch/{id}', [AttendanceController::class, 'punch'])->name('attendance.punch');

Route::get('/staff/attendance/', [EmployeeController::class, 'index'])->name('employee.index');

Route::delete('/breaktime/delete/{id}', [BreaktimeController::class, 'destroy'])->name('breaktime.destroy');
