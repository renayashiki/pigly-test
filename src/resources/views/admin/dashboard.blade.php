@extends('layouts.admin')

@section('title', '体重管理ダッシュボード')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')
@php
    // --- ダミーデータ ---
    $latestWeight = 45.0; // 最新体重
    $targetWeight = 46.5; // 目標体重
    $diffToTarget = $targetWeight - $latestWeight;
    $diffText = sprintf("%s%.1f kg", ($diffToTarget > 0 ? '+' : ''), $diffToTarget);

    // FN016: 体重管理一覧データ
    $weightLogs = [
        ['date' => '2023/11/26', 'weight' => 45.0, 'calories' => 1200, 'exercise_time' => '02:15', 'id' => 1],
        ['date' => '2023/11/25', 'weight' => 46.5, 'calories' => 1300, 'exercise_time' => '02:00', 'id' => 2],
        ['date' => '2023/11/24', 'weight' => 45.5, 'calories' => 1200, 'exercise_time' => '01:30', 'id' => 3],
        ['date' => '2023/11/23', 'weight' => 45.0, 'calories' => 1000, 'exercise_time' => '00:15', 'id' => 4],
        ['date' => '2023/11/22', 'weight' => 46.5, 'calories' => 1200, 'exercise_time' => '02:15', 'id' => 5],
        ['date' => '2023/11/21', 'weight' => 45.0, 'calories' => 1000, 'exercise_time' => '02:00', 'id' => 6],
        ['date' => '2023/11/20', 'weight' => 46.5, 'calories' => 1300, 'exercise_time' => '01:30', 'id' => 7],
        ['date' => '2023/11/19', 'weight' => 45.5, 'calories' => 1200, 'exercise_time' => '00:15', 'id' => 8],
    ];

    // 検索結果のダミー
    $isSearching = false; // 検索結果表示中をシミュレート
    $searchCount = count($weightLogs);
    $searchRange = "2023/11/10 〜 2023/11/30";
@endphp

    <div class="dashboard-container">
        <div class="summary-area">
            <div class="summary-card">
                <span class="label">目標体重 (FN016-6)</span>
                <span class="value">{{ sprintf('%.1f kg', $targetWeight) }}</span>
            </div>
            <div class="summary-card diff @if($diffToTarget > 0) positive @endif">
                <span class="label">目標まで</span>
                <span class="value">{{ $diffText }}</span>
            </div>
            <div class="summary-card">
                <span class="label">最新(現在)体重 (FN016-8)</span>
                <span class="value">{{ sprintf('%.1f kg', $latestWeight) }}</span>
            </div>
        </div>

        <div class="action-bar">
            <button type="button" class="btn-secondary search-button" onclick="openModal('search-modal')">検索</button>
            
            @if($isSearching)
                <button type="button" class="btn-reset" onclick="window.location='{{ route('weight-logs') }}'">リセット</button>
            @endif

            <button type="button" class="btn-primary data-add-button" onclick="openModal('register-modal')">データを追加</button>
        </div>

        @if($isSearching)
            <div class="search-info">
                {{ $searchRange }}の検索結果 {{ $searchCount }}件
            </div>
        @endif

        <div class="log-table-container">
            <table class="log-table">
                <thead>
                    <tr>
                        <th class="date-col">日付</th>
                        <th class="weight-col">体重</th>
                        <th class="calorie-col">食事摂取カロリー</th>
                        <th class="time-col">運動時間</th>
                        <th class="action-col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weightLogs as $log)
                    <tr class="log-item">
                        <td class="date-col">{{ $log['date'] }}</td> 
                        <td class="weight-col">{{ sprintf('%.1f kg', $log['weight']) }}</td>
                        <td class="calorie-col">{{ $log['calories'] }} cal</td>
                        <td class="time-col">{{ $log['exercise_time'] }}</td>
                        <td class="action-col">
                            <a href="{{ route('edit_log', ['weightLogId' => $log['id']]) }}" class="btn-icon">
                                <svg class="icon-pencil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-area">
            <div class="pagination-links">
                <a href="#" class="page-link disabled">&lt;</a>
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">&gt;</a>
            </div>
        </div>
    </div>

    <div id="register-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Weight Log 登録</h2>
            </div>
            
            <form method="POST" action="{{ route('store_log') }}" class="modal-form">
                @csrf
                <div class="form-group">
                    <label for="reg_date">日付</label>
                    <input id="reg_date" type="date" name="date" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label for="reg_weight">体重</label>
                    <input id="reg_weight" type="number" name="weight" step="0.1" placeholder="例: 45.0" required>
                    <span class="unit">kg</span>
                </div>

                <div class="form-group">
                    <label for="reg_calories">食事摂取カロリー</label>
                    <input id="reg_calories" type="number" name="calories" required>
                    <span class="unit">cal</span>
                </div>

                <div class="form-group">
                    <label for="reg_exercise_time">運動時間</label>
                    <input id="reg_exercise_time" type="time" name="exercise_time" required value="00:00">
                </div>

                <div class="form-group">
                    <label for="reg_exercise_content">運動内容</label>
                    <textarea id="reg_exercise_content" name="exercise_content" maxlength="120" rows="3"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('register-modal')">戻る</button>
                    <button type="submit" class="btn-primary">登録</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="search-modal" class="modal-overlay hidden">
        <div class="modal-content small-modal">
            <div class="modal-header">
                <h2>検索</h2>
            </div>
            
            <form method="GET" action="{{ route('weight-logs') }}" class="modal-form">
                <div class="form-group">
                    <label for="search_date_from">日付 (古い日付) (FN017-a)</label>
                    <input id="search_date_from" type="date" name="date_from">
                </div>

                <div class="form-group">
                    <label for="search_date_to">日付 (新しい日付) (FN017-a)</label>
                    <input id="search_date_to" type="date" name="date_to">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('search-modal')">戻る</button>
                    <button type="submit" class="btn-primary">検索</button>
                </div>
            </form>
        </div>
    </div>
@endsection