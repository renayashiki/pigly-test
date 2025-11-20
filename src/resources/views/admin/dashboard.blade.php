@extends('layouts.admin')

@section('title', '体重管理ダッシュボード')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')
@php
    // --- DB値の利用と安全策 ---
    $latestLog = $weightLogs->isNotEmpty() ? $weightLogs->first() : null;
    $latestWeight = $latestLog ? $latestLog->weight : 0.0;
    $targetWeight = $targetWeight ?? $latestWeight;
    $diffToTarget = $targetWeight - $latestWeight;
    $diffText = sprintf("%s%.1f kg", ($diffToTarget > 0 ? '+' : ''), $diffToTarget);

    // --- 検索関連のロジックを強化 ---
    $isSearching = request()->has('date_from') || request()->has('date_to');
    $searchCount = $weightLogs->total();
    $dateFrom = request('date_from');
    $dateTo = request('date_to');
    $searchRange = '';

    if (strval($dateFrom) && strval($dateTo)) {
        $searchRange = str_replace('-', '/', $dateFrom) . ' 〜 ' . str_replace('-', '/', $dateTo);
    } elseif (strval($dateFrom)) {
        $searchRange = str_replace('-', '/', $dateFrom) . ' 以降';
    } elseif (strval($dateTo)) {
        $searchRange = str_replace('-', '/', $dateTo) . ' 以前';
    } else {
        $searchRange = '全期間';
    }

    $searchMessage = ($isSearching && $searchCount > 0)
        ? "{$searchRange}の検索結果 {$searchCount}件"
        : (($isSearching && $searchCount === 0)
            ? "{$searchRange}の検索結果 0件"
        : "");
    $oldDateFrom = request('date_from');
    $oldDateTo = request('date_to');

@endphp

    <div class="dashboard-container">
        {{-- アラートメッセージ --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error">入力内容にエラーがあります。入力内容をご確認ください。</div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    openModal('register-modal');
                });
            </script>
        @endif
        {{-- サマリーエリア --}}
        <div class="summary-area">
            <div class="summary-card-group">
                <div class="summary-item">
                    <span class="label">目標体重</span>
                    <span class="value">{{ sprintf('%.1f kg', $targetWeight) }}</span>
                </div>
                <div class="summary-item diff @if($diffToTarget > 0) positive @endif">
                    <span class="label">目標まで</span>
                    <span class="value">{{ $diffText }}</span>
                </div>
                <div class="summary-item">
                    <span class="label">最新体重</span>
                    <span class="value">{{ sprintf('%.1f kg', $latestWeight) }}</span>
                </div>
            </div>
        </div>
        <div class="log-table-card">
            {{-- 検索フォーム (action-bar) --}}
            <form method="GET" action="{{ route('search_logs') }}" class="action-bar search-form-inline">
                <div class="form-group-inline date-from">
                    <input id="date_from" type="date" name="date_from" value="{{ $oldDateFrom }}">
                </div>
                <div class="form-group-inline date-to">
                    <label for="date_to">～</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $oldDateTo }}">
                </div>
                <button type="submit" class="btn-secondary search-button">検索</button>
                @if($isSearching)
                    <button type="button" class="btn-reset" onclick="window.location='{{ route('weight-logs') }}'">リセット</button>
                @endif
                <button type="button" class="btn-primary data-add-button" onclick="openModal('register-modal')">データを追加</button>
            </form>
            {{-- 検索結果情報 (FN017-c) --}}
            @if($isSearching)
                <div class="search-info">
                    {{ $searchMessage }}
                </div>
            @endif

            {{-- ログテーブル本体 --}}
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
                    {{-- DBから取得したログデータを表示 --}}
                    @forelse ($weightLogs as $log)
                    <tr class="log-item">
                        <td class="date-col">{{ $log->date->format('Y/m/d') }}</td>
                        <td class="weight-col">{{ sprintf('%.1f kg', $log->weight) }}</td>
                        <td class="calorie-col">{{ $log->calories }} cal</td>
                        <td class="time-col">{{ \Carbon\Carbon::parse($log->exercise_time)->format('H:i') }}</td>
                        <td class="action-col">
                            {{-- 詳細/更新画面へのリンク --}}
                            <a href="{{ route('edit_log', ['weightLogId' => $log->id]) }}" class="btn-icon">
                                <svg class="icon-pencil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="no-data">
                            @if($isSearching)
                                検索条件に一致する体重ログはありません。
                            @else
                                まだ体重ログが登録されていません。
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-area">
            {{ $weightLogs->appends(request()->query())->links('vendor.pagination.default') }}
        </div>
    </div>

    {{--ログ登録モーダル --}}
    <div id="register-modal" class="modal-overlay @if($errors->any()) visible @else hidden @endif">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Weight Logを追加</h2>
            </div>
            <form method="POST" action="{{ route('store_log') }}" class="modal-form" novalidate>
                @csrf
                <div class="form-group">
                    <label for="reg_date">日付</label>
                    <input id="reg_date" type="date" name="date" required value="{{ old('date') }}" placeholder="年/月/日">
                    @error('date')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="reg_weight">体重</label>
                    <div class="input-unit-wrapper">
                        <input id="reg_weight" type="text" name="weight" step="0.1" placeholder="50.0" required value="{{ old('weight') }}">
                        <span class="unit">kg</span>
                    </div>
                    @error('weight')
                        @foreach ($errors->get('weight') as $message)
                            <span class="error-message">{{ $message }}</span>
                        @endforeach
                    @enderror
                </div>
                <div class="form-group">
                    <label for="reg_calories">食事摂取カロリー</label>
                    <div class="input-unit-wrapper">
                        <input id="reg_calories" type="text" name="calories" required value="{{ old('calories') }}" placeholder="1200">
                        <span class="unit">cal</span>
                    </div>
                    @error('calories')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="reg_exercise_time">運動時間</label>
                    <input
                        id="reg_exercise_time"
                        type="time"
                        name="exercise_time"
                        required
                        value="{{ old('exercise_time', '') }}"
                        placeholder="00:00"
                        pattern="\d{2}:\d{2}"
                        title="00:00形式で入力してください"
                        maxlength="5"
                    >
                    @error('exercise_time')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group no-unit not-required">
                    <label for="reg_exercise_content">運動内容</label>
                    <textarea id="reg_exercise_content" name="exercise_content" maxlength="120" rows="3" placeholder="運動内容を追加">{{ old('exercise_content') }}</textarea>
                    @error('exercise_content')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('register-modal')">戻る</button>
                    <button type="submit" class="btn-primary">登録</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('visible');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('visible');
            document.getElementById(id).classList.add('hidden');
        }
    </script>
@endsection