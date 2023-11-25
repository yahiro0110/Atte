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
    <div class="alert">
        @if (session('message'))
            <div class="alert__success">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="content">
        <div class="content__message">{{ auth()->user()->name }}さん、毎日の勤怠管理は大切な仕事です。記録を忘れずに入力しましょう。</div>
        <div class="content__title">
            <ul>
                <li id="prevMonth">
                    <a>&lt;</a>
                </li>
                <li>
                    <span class="content__title-date" id="currentDate"></span>
                </li>
                <li id="nextMonth">
                    <a>&gt;</a>
                </li>
            </ul>
        </div>
        <div class="content__table">
            <table class="content__table-container">
                <tr>
                    <th class="content__table-td-date">日付</th>
                    <th class="content__table-td-time">勤務開始</th>
                    <th class="content__table-td-time">勤務終了</th>
                    <th class="content__table-td-time">休憩時間</th>
                    <th class="content__table-td-time">勤務時間</th>
                    <th></th>
                </tr>
                @foreach ($results as $attendance)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                        $isWeekend = $carbonDate->isWeekend();
                    @endphp
                    <tr class="{{ $isWeekend ? 'content__table-tr-weekend' : '' }}">
                        <td class="content__table-td-date">
                            {{ \Carbon\Carbon::parse($attendance->date)->locale('ja')->isoFormat('M/D(dd)') }}
                        </td>
                        <td class="content__table-td-time">
                            {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}
                        </td>
                        <td class="content__table-td-time">
                            {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                        </td>
                        <td class="content__table-td-time">
                            @if ($attendance->work_status == 6)
                                {{ \Carbon\Carbon::parse('00:00:00')->format('H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($breakTimes[$attendance->id])->format('H:i') }}
                            @endif
                        </td>
                        <td class="content__table-td-time">
                            @if ($attendance->work_status == 6)
                                {{ \Carbon\Carbon::parse('00:00:00')->format('H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($workedTimes[$attendance->id])->format('H:i') }}
                            @endif
                        </td>
                        <td>
                            @if (@isset($attendance->id))
                                <a href="{{ route('attendance.show', ['id' => $attendance->id]) }}"
                                    class="content__table-btn">編集</a>
                            @else
                                <a href="{{ route('attendance.show', ['id' => 0, 'employee_id' => $attendance->employee_id, 'date' => $attendance->date, 'work_status' => $attendance->work_status]) }}"
                                    class="content__table-btn">編集</a>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>

        </div>
    </div>
    <script src="{{ asset('js/displayCurrentMonth.js') }}"></script>
@endsection
