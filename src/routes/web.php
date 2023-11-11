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

Route::get('/', [EmployeeController::class, 'show'])->name('employee.home');
Route::get('/attendance/{id}', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/edit/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
