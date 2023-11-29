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
                <div class="content__form-error-message">{{ $message }}</div>
            @enderror
            @if ($type == 'manager')
                <div class="content__form-title">ログイン（マネージャ）</div>
            @else
                <div class="content__form-title">ログイン</div>
            @endif
            <div class="content__form-item">
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="メールアドレス">
                @error('email')
                    <div class="content__form-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="content__form-item">
                <input type="password" name="password" id="password" placeholder="パスワード">
                @error('password')
                    <div class="content__form-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="content__form-button">
                <button type="submit">ログイン</button>
            </div>
        </form>
        <div class="content__item">
            <p>アカウントをお持ちでない方はこちらから</p>
            @if ($type == 'manager')
                <a href="{{ route('employee.createForm', ['type' => $type]) }}">新規登録</a>
            @else
                <a href="{{ route('employee.createForm') }}">新規登録</a>
            @endif
        </div>
    </div>
    <script src="{{ asset('js/inputErrorStyles.js') }}"></script>
@endsection
