<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('X-CSRF-TOKEN')
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="#" class="header__logo">
                <img src="{{ asset('img/logo.svg') }}" alt="Your SVG Image">
            </a>
            @yield('nav')
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div>Atte, inc.</div>
    </footer>
</body>

</html>
