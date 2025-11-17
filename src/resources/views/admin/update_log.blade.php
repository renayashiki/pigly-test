@extends('layouts.admin')

@section('title', '情報更新画面')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/update.css') }}">
@endsection

@section('content')
@php
    // --- ダミーデータ ---
    // コントローラーから渡される想定
    $weightLogId = $weightLogId ?? 1; 
    $logData = [
        'date' => '2023-11-18', 
        'weight' => 45.0, 
        'calories' => 1200, 
        'exercise_time' => '00:15', 
        'exercise_content' => '運動内容のテキストが入ります。最大120文字。',
    ];
@endphp

<div class="log-form-container">
    <div class="form-header">
        <h2>Weight Log 詳細 (ID: {{ $weightLogId }})</h2>
        <form method="POST" action="{{ route('delete_log', ['weightLogId' => $weightLogId]) }}" class="delete-form" onsubmit="return confirm('本当にこのログを削除しますか？')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-icon-delete" title="ログを削除">
                <svg class="icon-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        </form>
    </div>
    
    <form method="POST" action="{{ route('update_log', ['weightLogId' => $weightLogId]) }}" class="log-update-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="update_date">日付</label>
            <input id="update_date" type="date" name="date" required value="{{ $logData['date'] }}">
        </div>

        <div class="form-group">
            <label for="update_weight">体重</label>
            <input id="update_weight" type="number" name="weight" step="0.1" placeholder="例: 45.0" required value="{{ $logData['weight'] }}">
            <span class="unit">kg</span>
        </div>

        <div class="form-group">
            <label for="update_calories">食事摂取カロリー</label>
            <input id="update_calories" type="number" name="calories" required value="{{ $logData['calories'] }}">
            <span class="unit">cal</span>
        </div>

        <div class="form-group">
            <label for="update_exercise_time">運動時間</label>
            <input id="update_exercise_time" type="time" name="exercise_time" required value="{{ $logData['exercise_time'] }}">
        </div>

        <div class="form-group">
            <label for="update_exercise_content">運動内容</label>
            <textarea id="update_exercise_content" name="exercise_content" maxlength="120" rows="3">{{ $logData['exercise_content'] }}</textarea>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="window.location='{{ route('weight-logs') }}'">戻る</button>
            <button type="submit" class="btn-primary">登録</button>
        </div>
    </form>
</div>
@endsection