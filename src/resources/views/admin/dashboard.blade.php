@extends('layouts.app')

@section('title', '体重管理ダッシュボード')

@section('content')
<div class="form-container" style="max-width: 500px; padding: 50px;">
    <div class="logo">PiGLy</div>
    <div class="subtitle">体重管理ダッシュボードへようこそ！</div>
    
    <!-- 認証済みユーザー名の表示 -->
    <h2 style="font-size: 1.5rem; color: var(--color-primary); margin-bottom: 20px;">
        {{ Auth::user()->name }} さんのページ
    </h2>
    
    <!-- ログアウトフォーム -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-primary" style="background-color: #777;">ログアウト</button>
    </form>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <p style="color: var(--color-text-sub);">
            ここから体重記録や目標管理の機能を作成していきます。
            この画面が見えていれば、認証とリダイレクトは成功です！
        </p>
    </div>
</div>
@endsection