@extends('layouts.main')

@section('title')
    日付編集
@endsection

@section('X-CSRF-TOKEN')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('nav')
    @include('layouts.nav')
@endsection

@section('content')
    <div class="content">
        <div class="content__message">{{ auth()->user()->name }}さん、毎日の勤怠管理は大切な仕事です。記録を忘れずに入力しましょう。</div>
        <div class="content__title">
            {{ \Carbon\Carbon::parse($results->date)->locale('ja')->isoFormat('Y年M月D日(dd)') }}の編集
        </div>
        <div class="content__form">
            <form action="{{ route('attendance.update', ['id' => $attendanceId]) }}" method="post">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $results->employee_id }}">
                <input type="hidden" name="date" value="{{ $results->date }}">
                <div class='content__form-title'>勤務時間の調整</div>
                <div class='content__form-inputarea'>
                    <label for="">勤務開始</label>
                    <span>{{ \Carbon\Carbon::parse($results->start_time)->format('H:i') }}</span>
                    <span class="content__form-inputarea-allow">&#9654;</span>
                    <input type="time" name="start_time"
                        value="{{ old('start_time') ? old('start_time') : \Carbon\Carbon::parse($results->start_time)->format('H:i') }}">
                    @error('start_time')
                        <div class="content__form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class='content__form-inputarea'>
                    <label for="">勤務終了</label>
                    <span>{{ \Carbon\Carbon::parse($results->end_time)->format('H:i') }}</span>
                    <span class="content__form-inputarea-allow">&#9654;</span>
                    <input type="time" name="end_time"
                        value="{{ old('end_time') ? old('end_time') : \Carbon\Carbon::parse($results->end_time)->format('H:i') }}">
                    @error('end_time')
                        <div class="content__form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class='content__form-title'>休憩時間の調整</div>
                <div class='content__form-inputarea breaktime'>
                    <label for="">休憩時間</label>
                    <span>{{ \Carbon\Carbon::parse($breakTime)->format('H:i') }}</span>
                    <span class="content__form-inputarea-allow">&#9654;</span>
                    <input type="time" value="{{ $breakTime }}" readonly class="calculatetimes">
                </div>
                <div class="content__form-inputsubarea">
                    <div class="content__form-inputsubarea-button">
                        <button type="button" id="add-break-time">休憩時間追加</button>
                    </div>
                    @if ($results->breaktimes)
                        @foreach ($results->breaktimes as $breaktime)
                            <div class="content__form-inputsubarea-time">
                                <input type="hidden" name="breaktime_ids[]" value="{{ $breaktime->id }}">
                                <label for="">{{ $loop->iteration }}回目</label>
                                <input type="time" name="breaktime_start_time[]" value="{{ $breaktime->start_time }}">
                                <span>-</span>
                                <input type="time" name="breaktime_end_time[]" value="{{ $breaktime->end_time }}">
                                <button type="button" class="delete-btn" value="{{ $breaktime->id }}">削除</button>
                                <div class="content__form-error" style="display: none;">終了時刻は開始時刻よりも後でなければなりません</div>
                                @error("breaktime_end_time.{$loop->index}")
                                    <div class="content__form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="content__form-button">
                    <button type="submit" class="content__form-inputsubarea-add">保存する</button>
                    <a href="{{ route('attendance.index', ['id' => $results->employee_id]) }}">一覧に戻る</a>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/breakTimeSetup.js') }}"></script>
@endsection
