@extends('layouts.admin')

@section('title', '情報更新画面')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/update.css') }}">
@endsection

@section('content')
@php
    // --- コントローラーからのデータ受け取りを前提とする ---
    // $weightLogId: URLから渡されたログのID (Controllerで定義済み)
    // $logData: データベースから取得した WeightLog モデルインスタンス (Controllerで定義済み)
    
    // 変数が未定義の場合のエラーを避けるための安全策
    $weightLogId = $weightLogId ?? null;
    $logData = $logData ?? (object)[
        'date' => '', 
        'weight' => '', 
        'calories' => '', 
        'exercise_time' => '', 
        'exercise_content' => '',
    ];

    // --- 修正点: 日付と時間の表示形式を明示的に指定 ---
    // input type="date" は 'Y-m-d' (YYYY-MM-DD) 形式を要求
    $formattedDate = '';
    if ($logData->date) {
        try {
            // Carbon::parse()を使って確実にCarbonオブジェクトにする
            $formattedDate = \Carbon\Carbon::parse($logData->date)->format('Y-m-d');
        } catch (\Exception $e) {
            $formattedDate = $logData->date;
        }
    }

    // input type="time" は 'H:i' (HH:MM) 形式を要求
    $formattedTime = '';
    if ($logData->exercise_time) {
        try {
            // DBによっては time 型が 'H:i:s' で返されるため、'H:i' に変換
            $formattedTime = \Carbon\Carbon::parse($logData->exercise_time)->format('H:i');
        } catch (\Exception $e) {
            $formattedTime = $logData->exercise_time;
        }
    }
    
    // 他のデータはそのまま
    $weightValue = $logData->weight ?? '';
    $caloriesValue = $logData->calories ?? '';
    $exerciseContent = $logData->exercise_content ?? '';
@endphp

<div class="log-form-container">
    <div class="form-header">
        <h2>Weight Log</h2>
    </div>
    
    <form method="POST" action="{{ route('update_log', ['weightLogId' => $weightLogId]) }}" class="log-update-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="update_date">日付</label>
            <input id="update_date" type="date" name="date" required value="{{ $formattedDate }}">
        </div>

        <div class="form-group unit-group">
            <label for="update_weight">体重</label>
            <input id="update_weight" type="text" name="weight" step="0.1" placeholder="例: 45.0" required value="{{ $weightValue }}">
            <span class="unit">kg</span>
        </div>

        <div class="form-group unit-group">
            <label for="update_calories">摂取カロリー</label>
            <input id="update_calories" type="text" name="calories" required value="{{ $caloriesValue }}">
            <span class="unit">cal</span>
        </div>

        <div class="form-group">
            <label for="update_exercise_time">運動時間</label>
            <input id="update_exercise_time" type="time" name="exercise_time" required value="{{ $formattedTime }}">
        </div>

        <div class="form-group no-unit">
            <label for="update_exercise_content">運動内容</label>
            <textarea id="update_exercise_content" name="exercise_content" maxlength="120" rows="3">{{ $exerciseContent }}</textarea>
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
