@extends('layouts.main')

@section('title')
    警告
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/warning.css') }}">
@endsection

@section('content')
    <div class="content">
        <div class="content__item">
            <div class="content__item-message">ログインが必要です</div>
            <p>5秒後にログインページにリダイレクトされます</p>
        </div>
        <div class="content__image">
            <img src="{{ asset('img/warning.svg') }}" alt="Your SVG Image">
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('employee.login') }}";
        }, 5000);
    </script>
@endsection
