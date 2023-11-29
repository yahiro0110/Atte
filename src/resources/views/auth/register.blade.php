@extends('layouts.main')

@section('title')
    ユーザ登録
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <div class="content">
        <form action="{{ route('employee.store') }}" method="POST" class="content__form">
            @csrf
            @if ($type == 'manager')
                <div class="content__form-title">新規登録（マネージャ）</div>
            @else
                <div class="content__form-title">新規登録</div>
            @endif
            <div class="content__form-item">
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="名前">
                @error('name')
                    <div class="content__form-error">{{ $message }}</div>
                @enderror
            </div>
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
            <div class="content__form-item">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="確認用パスワード">
            </div>
            @if ($type == 'manager')
                <input type="hidden" name="role" value="1">
            @else
                <input type="hidden" name="role" value="2">
            @endif
            <div class="content__form-button">
                <button type="submit">新規登録</button>
            </div>
        </form>
        <div class="content__item">
            <p>アカウントをお持ちの方はこちらから</p>
            @if ($type == 'manager')
                <a href="{{ route('employee.loginForm', ['type' => $type]) }}">ログイン</a>
            @else
                <a href="{{ route('employee.loginForm') }}">ログイン</a>
            @endif
        </div>
    </div>
    <script src="{{ asset('js/inputErrorStyles.js') }}"></script>
@endsection
