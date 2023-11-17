@extends('layouts.main')

@section('title')
    打刻
@endsection

@section('X-CSRF-TOKEN')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/punch.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:ital,wght@1,700&display=swap" rel="stylesheet">
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
                        予期せぬエラーが発生しました。システム管理者にお問い合わせください。<br />
                        session('work_status') = {{ session('work_status') }}
                @endswitch
            @else
                勤怠管理で業務がスムーズに。打刻のご協力ありがとうございます。
            @endif
        </div>
        <div id="timerDisplay"></div>
        <div class="content__image" id="imageContainer" data-clockin-img="{{ asset('img/punch-clockIn.svg') }}"
            data-onbreak-img="{{ asset('img/punch-onBreak.svg') }}"
            data-offbreak-img="{{ asset('img/punch-offBreak.svg') }}"
            data-clockout-img="{{ asset('img/punch-clockOut.svg') }}" data-error-img="{{ asset('img/punch-error.svg') }}">
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
                        <img src="{{ asset('img/punch-error.svg') }}" alt="Your SVG Image">
                @endswitch
            @else
                <img src="{{ asset('img/punch-default.svg') }}" alt="Your SVG Image">
            @endif
        </div>
        <div class="content__button">
            <div class="content__button-item">
                <form id="clockIn">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="employee_name" value="{{ $result->name }}">
                    <input type="hidden" name="punch_type" value="clockIn">
                    <button type="submit" {{ session('work_status') == null ? '' : 'disabled' }}
                        id="clockInButton">勤務開始</button>
                </form>
            </div>
            <div class="content__button-item">
                <form id="clockOut">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="employee_name" value="{{ $result->name }}">
                    <input type="hidden" name="punch_type" value="clockOut">
                    <button type="submit"
                        {{ session('work_status') == 1 || session('work_status') == 3 ? '' : 'disabled' }}
                        id="clockOutButton">勤務終了</button>
                </form>
            </div>
            <div class="content__button-item">
                <form id="onBreak">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="employee_name" value="{{ $result->name }}">
                    <input type="hidden" name="punch_type" value="onBreak">
                    <button type="submit"
                        {{ session('work_status') == 1 || session('work_status') == 3 ? '' : 'disabled' }}
                        id="onBreakButton">休憩開始</button>
                </form>
            </div>
            <div class="content__button-item">
                <form id="offBreak">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $result->id }}">
                    <input type="hidden" name="employee_name" value="{{ $result->name }}">
                    <input type="hidden" name="punch_type" value="offBreak">
                    <button type="submit" {{ session('work_status') == 2 ? '' : 'disabled' }}
                        id="offBreakButton">休憩終了</button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/punchSetup.js') }}"></script>
@endsection
