@extends('layouts.main')

@section('title')
    日付一覧
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('nav')
    @include('layouts.nav')
@endsection

@section('content')
    <div class="content">
        <div class="content__message">HOGEHOGEさん、毎日の勤怠管理は大切な仕事です。記録を忘れずに入力しましょう。</div>
        <div class="content__table">
            <table>
                <tr>
                    <th>日付</th>
                    <th>勤務開始</th>
                    <th>勤務終了</th>
                    <th>休憩時間</th>
                    <th>勤務時間</th>
                    <th></th>
                </tr>
                @foreach ($attendanceResults as $attendance)
                    <tr>
                        <td>{{ $attendance->date }}</td>
                        <td>{{ $attendance->start_time }}</td>
                        <td>{{ $attendance->end_time }}</td>
                        <td>{{ $totalBreakTimes[$attendance->id] }}</td>
                        <td>{{ $totalWorkedTimes[$attendance->id] }}</td>
                        <td>
                            <a class="btn" href="#">編集</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@php

@endphp
