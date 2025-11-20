@extends('layouts.admin')

@section('title', '体重管理ダッシュボード')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')
@php
    // --- DB値の利用と安全策 ---
    // $weightLogs は WeightLogController から渡されるページネーションオブジェクト
    // $targetWeight は WeightLogController から渡されるDB値（または null）

    // FN016-8: 最新体重の取得。ログがない場合は 0.0 を設定
    $latestLog = $weightLogs->isNotEmpty() ? $weightLogs->first() : null;
    $latestWeight = $latestLog ? $latestLog->weight : 0.0; 

    // FN016-6: 目標体重の取得。設定がない場合は最新体重と同じ値で初期化
    $targetWeight = $targetWeight ?? $latestWeight;

    // FN016-7: 目標までの差分計算
    $diffToTarget = $targetWeight - $latestWeight;
    $diffText = sprintf("%s%.1f kg", ($diffToTarget > 0 ? '+' : ''), $diffToTarget);

    // --- 検索関連のロジックを強化 ---
    // FN017: リクエストにdate_fromまたはdate_toがあれば、検索中と判定
    $isSearching = request()->has('date_from') || request()->has('date_to');
    $searchCount = $weightLogs->total(); // 検索結果の全件数を使用

    // FN017-c: 検索期間の表示テキストを動的に生成
    $dateFrom = request('date_from');
    $dateTo = request('date_to');
    $searchRange = '';

    if (strval($dateFrom) && strval($dateTo)) { // 両方設定されている場合
        $searchRange = str_replace('-', '/', $dateFrom) . ' 〜 ' . str_replace('-', '/', $dateTo);
    } elseif (strval($dateFrom)) { // 開始日のみ設定されている場合
        $searchRange = str_replace('-', '/', $dateFrom) . ' 以降';
    } elseif (strval($dateTo)) { // 終了日のみ設定されている場合
        $searchRange = str_replace('-', '/', $dateTo) . ' 以前';
    } else {
        $searchRange = '全期間';
    }
    
    // 検索メッセージの生成
    $searchMessage = ($isSearching && $searchCount > 0) 
        ? "{$searchRange}の検索結果 {$searchCount}件"
        : (($isSearching && $searchCount === 0) 
            ? "{$searchRange}の検索結果 0件"
            : "");
    
    // 検索フォームに以前の入力を保持するための変数
    $oldDateFrom = request('date_from');
    $oldDateTo = request('date_to');

@endphp

    <div class="dashboard-container">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error">入力内容にエラーがあります。入力内容をご確認ください。</div>
            <script>
                // バリデーションエラーがある場合、モーダルを自動で開く
                document.addEventListener('DOMContentLoaded', function() {
                    openModal('register-modal');
                });
            </script>
        @endif

        <div class="summary-area">
            {{-- 目標体重 (FN016-6) --}}
            <div class="summary-card">
                <span class="label">目標体重</span>
                <span class="value">{{ sprintf('%.1f kg', $targetWeight) }}</span>
            </div>
            {{-- 目標までの差分 (FN016-7) --}}
            <div class="summary-card diff @if($diffToTarget > 0) positive @endif">
                <span class="label">目標まで</span>
                <span class="value">{{ $diffText }}</span>
            </div>
            {{-- 最新体重 (FN016-8) --}}
            <div class="summary-card">
                <span class="label">最新体重</span>
                <span class="value">{{ sprintf('%.1f kg', $latestWeight) }}</span>
            </div>
        </div>
        
        {{-- 検索フォームとアクションバーを統合 --}}
        <div class="log-table-card">
            <form method="GET" action="{{ route('search_logs') }}" class="action-bar search-form-inline">

            {{-- FN017: 日付 (古い日付) (見本に合わせたインライン配置) --}}
            <div class="form-group-inline date-from">
                <input id="date_from" type="date" name="date_from" value="{{ $oldDateFrom }}">
            </div>

            {{-- FN017: 日付 (新しい日付) (見本に合わせたインライン配置) --}}
            <div class="form-group-inline date-to">
                <label for="date_to">～</label>
                <input id="date_to" type="date" name="date_to" value="{{ $oldDateTo }}">
            </div>
            
            {{-- 検索ボタン (FN017) --}}
            <button type="submit" class="btn-secondary search-button">検索</button>
            
            {{-- 検索リセットボタン (FN017-2) --}}
            @if($isSearching)
                <button type="button" class="btn-reset" onclick="window.location='{{ route('weight-logs') }}'">リセット</button>
            @endif

            {{-- データ追加ボタン (FN018-3) --}}
                <button type="button" class="btn-primary data-add-button" onclick="openModal('register-modal')">データを追加</button>
        </form>
        
        {{-- 検索結果情報 (FN017-c) --}}
        @if($isSearching)
            <div class="search-info">
                {{ $searchMessage }}
            </div>
        @endif

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
                        {{-- FN016-1: 日付フォーマット（Carbonインスタンスの利用） --}}
                        <td class="date-col">{{ $log->date->format('Y/m/d') }}</td> 
                        {{-- FN016-2: 体重フォーマット --}}
                        <td class="weight-col">{{ sprintf('%.1f kg', $log->weight) }}</td>
                        {{-- FN016-3: カロリー --}}
                        <td class="calorie-col">{{ $log->calories }} cal</td>
                        {{-- FN016-4: 運動時間 --}}
                        <td class="time-col">{{ \Carbon\Carbon::parse($log->exercise_time)->format('H:i') }}</td>
                        <td class="action-col">
                            {{-- FN016-5: 詳細/更新画面へのリンク --}}
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
            {{-- ページネーションリンクを表示 (FN016 ページネーションの要件) --}}
            {{-- appends(request()->query()) で検索条件を維持 --}}
            {{ $weightLogs->appends(request()->query())->links('vendor.pagination.default') }}
        </div>
    </div>

    {{-- FN018: ログ登録モーダル --}}
    <div id="register-modal" class="modal-overlay @if($errors->any()) visible @else hidden @endif">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Weight Logを追加</h2>
            </div>
            
            <form method="POST" action="{{ route('store_log') }}" class="modal-form" novalidate>
                @csrf
                {{-- 1. 日付 (FN025-1) --}}
                <div class="form-group">
                    <label for="reg_date">日付</label>
                    <input id="reg_date" type="date" name="date" required value="{{ old('date') }}" placeholder="年/月/日">
                    @error('date')<span class="error-message">{{ $message }}</span>@enderror 
                </div>

                {{-- 2. 体重 (FN025-2) --}}
                <div class="form-group">
                    <label for="reg_weight">体重</label>
                    <div class="input-unit-wrapper"> 
                        <input id="reg_weight" type="text" name="weight" step="0.1" placeholder="50.0" required value="{{ old('weight') }}">
                        <span class="unit">kg</span>
                    </div>
                    {{-- 複数のエラーメッセージをすべて表示するために $errors->get('weight') をループ --}}
                    @error('weight')
                        @foreach ($errors->get('weight') as $message)
                            <span class="error-message">{{ $message }}</span>
                        @endforeach
                    @enderror
                </div>

                {{-- 3. 食事摂取カロリー (FN025-3) --}}
                <div class="form-group">
                    <label for="reg_calories">食事摂取カロリー</label>
                    <div class="input-unit-wrapper"> 
                        <input id="reg_calories" type="text" name="calories" required value="{{ old('calories') }}" placeholder="1200">
                        <span class="unit">cal</span> 
                    </div>
                    @error('calories')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                {{-- 4. 運動時間 (FN025-4) --}}
                <div class="form-group">
                    <label for="reg_exercise_time">運動時間</label>
                    {{-- type="time"からtype="text"に変更し、placeholder="00:00"を設定。JavaScriptは削除 --}}
                    <input 
                        id="reg_exercise_time" 
                        type="time"
                        name="exercise_time" 
                        required 
                        value="{{ old('exercise_time', '') }}" 
                        placeholder="00:00"
                        pattern="\d{2}:\d{2}" {{-- 2桁:2桁 の形式を要求 --}}
                        title="00:00形式で入力してください" {{-- 形式が異なる場合のエラーメッセージ --}}
                        maxlength="5" {{-- HH:MM (5文字) の長さ制限 --}}
                        {{-- oninput="formatTime(this)" は削除しました --}}
                    >
                    @error('exercise_time')<span class="error-message">{{ $message }}</span>@enderror 
                </div>

                {{-- 5. 運動内容 (FN025-5) --}}
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
        // モーダル制御関数 (JavaScriptはこれだけ維持します)
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('visible');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('visible');
            document.getElementById(id).classList.add('hidden');
        }

        // 運動時間入力用のフォーマット関数はルールに従い削除しました
    </script>
@endsection
