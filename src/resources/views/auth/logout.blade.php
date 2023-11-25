@extends('layouts.main')

@section('title')
    ログアウト
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/logout.css') }}">
@endsection

@section('nav')
    @include('layouts.nav')
@endsection

@section('content')
    <div class="content">
        <div class="content__item">
            <div class="content__item-message">ログアウトしました</div>
            <p>もう一度ログインする場合はこちらから</p>
            <a href="{{ route('employee.login') }}">ログイン</a>
        </div>
    </div>
@endsection
