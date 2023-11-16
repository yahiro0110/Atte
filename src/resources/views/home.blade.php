@extends('layouts.main')

@section('title')
    Home
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('nav')
    @include('layouts.nav')
@endsection

@section('content')
    <div class="content">
        <div class="content__message">{{ $employee->name }}さん、おはようございます。</div>
        <div class="content__image">
            <img src="{{ asset('img/home.svg') }}" alt="Your SVG Image">
        </div>
        <div class="content__button">
            <a href="{{ route('employee.punch', ['id' => $employee->id]) }}">打刻</a>
        </div>
        @if ($employee->role == 1)
            <div class="content__button">
                <a href="#">スタッフ勤怠情報</a>
            </div>
        @endif
    </div>
    </div>
@endsection
