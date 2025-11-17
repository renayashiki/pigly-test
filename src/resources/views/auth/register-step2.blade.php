@extends('layouts.app')

@section('title', '初期目標体重登録 Step 2')

@section('styles')
    <!-- 画面固有のCSSを外部ファイルから読み込み -->
    <link rel="stylesheet" href="{{ asset('css/auth/step2.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">新規会員登録</div>
    <div class="subtitle">STEP2: 初期体重の入力</div>

    <!-- Step 2コントローラ経由でFortifyの登録アクションにPOST -->
    <form method="POST" action="{{ route('register.step2.store') }}">
        @csrf

        <!-- 1. 現在の体重 (FN007) -->
        <div class="form-group">
            <label for="current_weight">現在の体重</label>
            <div class="weight-input-group">
                <!-- FN009のバリデーションを考慮し、inputの属性を設定 -->
                <input id="current_weight" type="number" name="current_weight" step="0.1" min="0" max="999.9" value="{{ old('current_weight') }}" required>
                <span>kg</span>
            </div>
            @error('current_weight')
                <!-- FN009のメッセージがCreateNewUserのバリデーションによって表示される -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- 2. 目標の体重 (FN007) -->
        <div class="form-group">
            <label for="target_weight">目標の体重</label>
            <div class="weight-input-group">
                <!-- FN009のバリデーションを考慮し、inputの属性を設定 -->
                <input id="target_weight" type="number" name="target_weight" step="0.1" min="0" max="999.9" value="{{ old('target_weight') }}" required>
                <span>kg</span>
            </div>
            @error('target_weight')
                <!-- FN009のメッセージがCreateNewUserのバリデーションによって表示される -->
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- アカウント作成ボタン (FN010) -->
        <button type="submit" class="btn-primary">アカウント作成</button>
    </form>
</div>
@endsection