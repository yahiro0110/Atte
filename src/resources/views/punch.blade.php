@extends('layouts.main')

@section('title')
    打刻
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/punch.css') }}">
@endsection

@section('nav')
    @include('layouts.nav')
@endsection

@section('content')
    <div class="alert">
        @if (session('message') && session('message') !== 'date success')
            <div class="alert__success">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="content">
        <div class="content__message">
            @if (session('work_status'))
                @switch(session('work_status'))
                    @case(1)
                        {{ $result->name }}さん、今日も一日頑張りましょう！
                    @break

                    @case(2)
                        {{ $result->name }}さん、気分転換のために少し休憩しましょう。
                    @break

                    @case(3)
                        {{ $result->name }}さん、残りの勤務時間、頑張っていきましょう。
                    @break

                    @case(4)
                        {{ $result->name }}さん、今日の勤務、おつかれさまでした。
                    @break

                    @default
                        予期せぬエラーが発生しました。システム管理者にお問い合わせください。
                        session('work_status') = {{ session('work_status') }}
                @endswitch
            @else
                勤怠管理で業務がスムーズに。打刻のご協力ありがとうございます。
            @endif
        </div>
        <div class="content__image">
            @if (session('work_status'))
                @switch(session('work_status'))
                    @case(1)
                        <img src="{{ asset('img/punch-clockIn.svg') }}" alt="Your SVG Image">
                    @break

                    @case(2)
                        <img src="{{ asset('img/punch-onBreak.svg') }}" alt="Your SVG Image">
                    @break

                    @case(3)
                        <img src="{{ asset('img/punch-offBreak.svg') }}" alt="Your SVG Image">
                    @break

                    @case(4)
                        <img src="{{ asset('img/punch-clockOut.svg') }}" alt="Your SVG Image">
                    @break

                    @default
                        <img src="{{ asset('img/punch-default.svg') }}" alt="Your SVG Image">
                @endswitch
            @else
                <img src="{{ asset('img/punch-default.svg') }}" alt="Your SVG Image">
            @endif
        </div>
        <div class="content__button">
            <div class="content__button-item">
                <form action="{{ route('attendance.punch', ['id' => $result->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="punch_type" value="clockIn">
                    <button type="submit">勤務開始</button>
                </form>
            </div>
            <div class="content__button-item">
                <form action="{{ route('attendance.punch', ['id' => $result->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="punch_type" value="clockOut">
                    <button type="submit">勤務終了</button>
                </form>
            </div>
            <div class="content__button-item">
                <form action="{{ route('attendance.punch', ['id' => $result->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="punch_type" value="onBreak">
                    <button type="submit">休憩開始</button>
                </form>
            </div>
            <div class="content__button-item">
                <form action="{{ route('attendance.punch', ['id' => $result->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="punch_type" value="offBreak">
                    <button type="submit">休憩終了</button>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
