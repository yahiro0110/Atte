@extends('layouts.main')

@section('title')
    ログイン
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <div class="content">
        <form action="{{ route('employee.login') }}" method="POST" class="content__form">
            @csrf
            @error('error')
                <div class="content__form-error">{{ $message }}</div>
            @enderror
            @if (is_null($type))
                <div class="content__form-title">ログイン</div>
            @else
                <div class="content__form-title">ログイン（マネージャ）</div>
            @endif
            <div class="content__form-item">
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="メールアドレス">
            </div>
            <div class="content__form-item">
                <input type="password" name="password" id="password" placeholder="パスワード">
            </div>
            <div class="content__form-button">
                <button type="submit">ログイン</button>
            </div>
        </form>
    </div>
@endsection
