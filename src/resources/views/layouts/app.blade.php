<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PiGLy')</title>

    <!-- 共通アプリCSS (public/css/auth/app.css) を読み込み。
         Sanitize.cssはこのファイル内で@importされています。 -->
    <link rel="stylesheet" href="{{ asset('css/auth/app.css') }}">

    <!-- 各画面固有のCSSを読み込むためのPlaceholder -->
    @yield('styles')
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>