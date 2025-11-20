@extends('layouts.admin')

@section('title', '情報更新画面')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/update.css') }}">
@endsection

@section('content')
@php
    $weightLogId = $weightLogId ?? null;
    $logData = $logData ?? (object)[
        'date' => '',
        'weight' => '',
        'calories' => '',
        'exercise_time' => '',
        'exercise_content' => '',
    ];

    $formattedDate = '';
    if ($logData->date) {
        try {
            $formattedDate = \Carbon\Carbon::parse($logData->date)->format('Y-m-d');
        } catch (\Exception $e) {
            $formattedDate = $logData->date;
        }
    }

    $formattedTime = '';
    if ($logData->exercise_time) {
        try {
            $formattedTime = \Carbon\Carbon::parse($logData->exercise_time)->format('H:i');
        } catch (\Exception $e) {
            $formattedTime = $logData->exercise_time;
        }
    }

    $weightValue = old('weight', $logData->weight ?? '');
    $caloriesValue = old('calories', $logData->calories ?? '');
    $exerciseContent = old('exercise_content', $logData->exercise_content ?? '');
@endphp

<div class="log-form-container">
    <div class="form-header">
        <h2>Weight Log</h2>
    </div>
    <form method="POST" action="{{ route('update_log', ['weightLogId' => $weightLogId]) }}" class="log-update-form" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="update_date">日付</label>
            <input id="update_date" type="date" name="date" required value="{{ old('date', $formattedDate) }}">
            @error('date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group unit-group">
            <label for="update_weight">体重</label>
            <input id="update_weight" type="text" name="weight" step="0.1" placeholder="例: 45.0" required value="{{ $weightValue }}">
            <span class="unit">kg</span>
            @error('weight')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group unit-group">
            <label for="update_calories">摂取カロリー</label>
            <input id="update_calories" type="text" name="calories" required value="{{ $caloriesValue }}">
            <span class="unit">cal</span>
            @error('calories')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_exercise_time">運動時間</label>
            <input id="update_exercise_time" type="time" name="exercise_time" required value="{{ old('exercise_time', $formattedTime) }}">
            @error('exercise_time')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group no-unit">
            <label for="update_exercise_content">運動内容</label>
            <textarea id="update_exercise_content" name="exercise_content" maxlength="120" rows="3">{{ $exerciseContent }}</textarea>
            @error('exercise_content')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <div class="center-buttons">
                <button type="button" class="btn-secondary" onclick="window.location='{{ route('weight-logs') }}'">戻る</button>
                <button type="submit" class="btn-primary">登録</button>
            </div>

            <button type="button" class="btn-delete" title="ログを削除" onclick="showDeleteConfirmation()">
                <svg viewBox="0 0 24 24" fill="currentColor" class="icon-trash-custom">
                    <path d="M0 0h24v24H0V0z" fill="none"/>
                    <path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7z"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<div id="delete-confirmation-modal" class="custom-modal-overlay hidden">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3>確認</h3>
        </div>
        <div class="custom-modal-body">
            <p>本当にこのログを削除しますか？</p>
        </div>
        <div class="custom-modal-actions">
            <button type="button" class="btn-secondary" onclick="hideDeleteConfirmation()">キャンセル</button>
            
            <form id="delete-form" method="POST" action="{{ route('delete_log', ['weightLogId' => $weightLogId]) }}" style="margin: 0; display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-primary-delete">削除</button>
            </form>
        </div>
    </div>
</div>

<script>
    // モーダル表示
    function showDeleteConfirmation() {
        const modal = document.getElementById('delete-confirmation-modal');
        modal.classList.remove('hidden');
    }

    // モーダル非表示
    function hideDeleteConfirmation() {
        const modal = document.getElementById('delete-confirmation-modal');
        modal.classList.add('hidden');
    }

    // モーダル外クリックで閉じる機能
    document.getElementById('delete-confirmation-modal').addEventListener('click', function(e) {
        if (e.target.id === 'delete-confirmation-modal') {
            hideDeleteConfirmation();
        }
    });
</script>
@endsection