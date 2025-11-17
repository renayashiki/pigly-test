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
    <form method="POST" action="{{ route('update_goal') }}" class="goal-setting-form">
        @csrf
        @method('PUT')

        <!-- FN031-1: 目標の体重 (入力必須) -->
        <div class="form-group">
            <label for="goal_weight">目標の体重</label>
            {{-- $currentGoal がコントローラーから渡されればその値を表示し、なければ空欄にする --}}
            <input id="goal_weight" type="number" name="goal_weight" step="0.1" placeholder="例: 45.0" required value="{{ $currentGoal ?? '' }}">
            <span class="unit">kg</span>
        </div>

        <div class="form-actions">
            <!-- FN034-2: 戻るボタン -->
            <button type="button" class="btn-secondary" onclick="window.location='{{ route('weight-logs') }}'">戻る</button>
            <!-- FN034-1: 更新ボタン -->
            <button type="submit" class="btn-primary">更新</button>
        </div>
    </form>
</div>
@endsection