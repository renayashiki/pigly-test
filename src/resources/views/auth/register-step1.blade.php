@extends('layouts.auth')

@section('title', '新規会員登録 - Step 1')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/step1.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">新規会員登録</div>
    <div class="subtitle-detail">STEP1: アカウント情報の登録</div>
    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
        @csrf
        <div class="form-group">
            <label for="name">お名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" utofocus autocomplete="name" placeholder="名前を入力">
            @error('name')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="メールアドレスを入力">
            @error('email')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" autocomplete="new-password" placeholder="パスワードを入力">
            @error('password')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn-primary">次に進む</button>
    </form>
    <a href="{{ route('login') }}" class="sub-link">ログインはこちら</a>
</div>
@endsection