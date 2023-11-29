@extends('layouts.main')

@section('title')
    ログアウト
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/logout.css') }}">
@endsection

@section('content')
    <div class="content">
        <div class="content__item">
            <div class="content__item-message">ログアウトしました</div>
            <p>もう一度ログインする場合はこちらから</p>
            @if ($role == 1)
                <a href="{{ route('employee.loginForm', ['type' => 'manager']) }}">ログイン</a>
            @else
                <a href="{{ route('employee.loginForm') }}">ログイン</a>
            @endif
        </div>
    </div>
@endsection
