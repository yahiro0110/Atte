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
    <div class="content">
        <div class="content__message">勤怠管理で業務がスムーズに。打刻のご協力ありがとうございます。</div>
        <div class="content__image">
            <img src="{{ asset('img/punch-default.svg') }}" alt="Your SVG Image">
        </div>
        <div class="content__button">
            <div class="content__button-item">
                <a href="#">勤務開始</a>
            </div>
            <div class="content__button-item">
                <a href="#">勤務終了</a>
            </div>
            <div class="content__button-item">
                <a href="#">休憩開始</a>
            </div>
            <div class="content__button-item">
                <a href="#">休憩終了</a>
            </div>
        </div>
    </div>
    </div>
@endsection
