@extends('layouts.app')

@section('title', 'ログイン')

@section('styles')
    <!-- 画面固有のCSSを外部ファイルから読み込み -->
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">ログイン</div>

    <!-- FortifyのログインアクションにPOST (FN011) -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- 1. メールアドレス (FN012) -->
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <!-- FN014a, FN014bのエラーメッセージが表示される -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- 2. パスワード (FN012) -->
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <!-- FN014a -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Fortifyの認証失敗時のデフォルトエラーメッセージ表示 (FN014-3a) -->
        <!-- Fortifyは認証失敗時に通常 'email' エラーとしてメッセージを返します。
            ここでは、入力値のバリデーションエラーではなく、認証失敗のエラーの場合に表示します。 -->
        @if ($errors->has('email') && !$errors->has('password') && !old('email'))
            <span class="validation-error">{{ $errors->first('email') }}</span>
        @endif

        <!-- ログインボタン (FN015-1) -->
        <button type="submit" class="btn-primary">ログイン</button>
    </form>

    <!-- アカウント作成はこちらボタン (FN015-2) -->
    <a href="{{ route('register') }}" class="sub-link">アカウント作成はこちら</a>
</div>
@endsection