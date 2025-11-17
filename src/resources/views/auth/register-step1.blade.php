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
            {{-- required を削除 --}}
            <input id="name" type="text" name="name" value="{{ old('name') }}" utofocus autocomplete="name"> 
            @error('name')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            {{-- required を削除 --}}
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username"> 
            @error('email')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            {{-- required を削除 --}}
            <input id="password" type="password" name="password" autocomplete="new-password"> 
            @error('password')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- パスワード確認フィールドも通常ここに追加されます。もしStep1でパスワード確認がない場合は無視してください --}}

        <button type="submit" class="btn-primary">次に進む</button>
    </form>

    <a href="{{ route('login') }}" class="sub-link">ログインはこちら</a>
</div>
@endsection