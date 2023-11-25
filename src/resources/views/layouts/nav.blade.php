<div class="header__nav">
    <ul class="header__nav-wrapper">
        <li class="header__nav-item"><a href="{{ route('employee.home') }}">ホーム</a></li>
        <li class="header__nav-item"><a href="{{ route('attendance.index', ['id' => auth()->user()->id]) }}">日付一覧</a></li>
        <li class="header__nav-item"><a href="{{ route('employee.logout') }}">ログアウト</a></li>
    </ul>
</div>
