@extends('layouts.auth')

@section('title', 'ログイン')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">ログイン</div>


    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            {{-- ここにプレースホルダーを追加 --}}
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="メールアドレスを入力">
            @error('email')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            {{-- ここにプレースホルダーを追加 --}}
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="パスワードを入力">
            @error('password')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-primary">ログイン</button>
    </form>

    <a href="{{ route('register.step1') }}" class="sub-link">アカウント作成はこちら</a>
</div>
@endsection