@extends('layouts.main')

@section('title')
    スタッフ勤怠情報
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/staff_index.css') }}">
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
        <div class="content__message">HOGEHOGEさん、スタッフの勤務状況を確認し、健康管理にも注目お願いします。</div>
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
                    <th class="content__table-td-date">名前</th>
                    <th class="content__table-td-time">勤務開始</th>
                    <th class="content__table-td-time">勤務終了</th>
                    <th class="content__table-td-time">休憩時間</th>
                    <th class="content__table-td-time">勤務時間</th>
                </tr>
                @foreach ($results as $employee)
                    <tr class="">
                        <td class="content__table-td-name">
                            {{ $employee->name }}
                        </td>
                        @if ($employee->attendances->isEmpty())
                            <td class="content__table-td-time">00:00</td>
                            <td class="content__table-td-time">00:00</td>
                            <td class="content__table-td-time">00:00</td>
                            <td class="content__table-td-time">00:00</td>
                        @else
                            <td class="content__table-td-time">
                                {{ \Carbon\Carbon::parse($employee->attendances->first()->start_time)->format('H:i') }}
                            </td>
                            <td class="content__table-td-time">
                                {{ \Carbon\Carbon::parse($employee->attendances->first()->end_time)->format('H:i') }}
                            </td>
                            <td class="content__table-td-time">
                                {{ \Carbon\Carbon::parse($employee->attendances->first()->total_break_time)->format('H:i') }}
                            </td>
                            <td class="content__table-td-time">
                                {{ \Carbon\Carbon::parse($employee->attendances->first()->total_work_time)->format('H:i') }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <script src="{{ asset('js/displayCurrentDay.js') }}"></script>
@endsection
