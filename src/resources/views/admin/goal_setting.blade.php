@extends('layouts.admin')

@section('title', '目標体重設定画面')

@section('styles')
    <!-- ④ 目標体重設定画面固有のスタイル -->
    <link rel="stylesheet" href="{{ asset('css/admin/setting.css') }}">
@endsection

@section('content')
{{-- コントローラーからデータを受け取るように修正。
$currentGoal が渡されない場合は null となり、フォームの初期値は空欄になる。
--}}

<div class="goal-setting-container">
    <div class="form-header">
        <h2>目標体重設定</h2>
    </div>
    
    <!-- FN034-1: 更新ボタンは update_goal ルートを想定 -->
    <form method="POST" action="{{ route('update_goal') }}" class="goal-setting-form" novalidate >
        @csrf
        @method('PUT')

        <!-- FN031-1: 目標の体重 (入力必須) -->
        <div class="form-group">
            <input
                id="goal_weight"
                type="text"
                name="target_weight"
                step="0.1"
                placeholder="例: 45.0"
                required
                class="@error('target_weight') is-invalid @enderror"
                value="{{ old('target_weight', $currentGoal ?? '') }}"
            >
            <span class="unit">kg</span>

            @error('target_weight')
                <div class="validation-error-message">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-actions">
            <!--戻るボタン -->
            <button type="button" class="btn-secondary" onclick="window.location='{{ route('weight-logs') }}'">戻る</button>
            <!--更新ボタン -->
            <button type="submit" class="btn-primary">更新</button>
        </div>
    </form>
</div>
@endsection