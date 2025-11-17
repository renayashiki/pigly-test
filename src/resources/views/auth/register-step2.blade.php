@extends('layouts.auth')

@section('title', '初期目標体重登録 Step 2')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/step2.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="logo">PiGLy</div>
    <div class="subtitle">新規会員登録</div>
    <div class="subtitle-detail">STEP2: 初期体重の入力</div> 

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="current_weight">現在の体重</label>
            <div class="weight-input-group">
                <input id="current_weight" type="text" name="current_weight" step="0.1" value="{{ old('current_weight') }}" required>
                <span>kg</span>
            </div>
            @error('current_weight')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="target_weight">目標の体重</label>
            <div class="weight-input-group">
                <input id="target_weight" type="text" name="target_weight" step="0.1" value="{{ old('target_weight') }}" required>
                <span>kg</span>
            </div>
            @error('target_weight')
                <span class="validation-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-primary">登録する</button>
    </form>
</div>
@endsection