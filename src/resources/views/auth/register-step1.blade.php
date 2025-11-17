@extends('layouts.app')

@section('title', '新規会員登録 Step 1')

@section('styles')
    <!-- 画面固有のCSSを外部ファイルから読み込み -->
    <link rel="stylesheet" href="{{ asset('css/auth/step1.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">新規会員登録</div>
    <div class="subtitle">STEP1: アカウント情報の登録</div>

    <!-- Fortifyの登録アクションにPOST (CreateNewUser::createが呼ばれる) -->
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- 1. お名前 (FN002) -->
        <div class="form-group">
            <label for="name">お名前</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <!-- FN004a: お名前を入力してください -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- 2. メールアドレス (FN002) -->
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <!-- FN004a, FN004bのメッセージがFortifyカスタマイズによって表示される -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- 3. パスワード (FN002) -->
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <!-- FN004a -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- 次に進むボタン (FN005) -->
        <button type="submit" class="btn-primary">次に進む</button>
    </form>

    <!-- ログインはこちらボタン (FN006-1) -->
    <a href="{{ route('login') }}" class="sub-link">ログインはこちら</a>
</div>
@endsection