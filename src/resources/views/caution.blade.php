@extends('layouts.main')

@section('title')
    注意
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/warning.css') }}">
@endsection

@section('content')
    <div class="content">
        <div class="content__item">
            <div class="content__item-message">閲覧できる権限がありません</div>
            <p>5秒後にホームページにリダイレクトされます</p>
        </div>
        <div class="content__image">
            <img src="{{ asset('img/warning.svg') }}" alt="Your SVG Image">
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('employee.home') }}";
        }, 5000);
    </script>
@endsection
