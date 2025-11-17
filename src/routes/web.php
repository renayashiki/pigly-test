<?php

use Illuminate\Support\Facades\Route;
// AuthController を使用
use App\Http\Controllers\AuthController;
// Fortifyのデフォルトコントローラー
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
// カスタムコントローラー
use App\Http\Controllers\LogoutRedirectController;
use App\Http\Controllers\WeightLogController;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Fortify;


Fortify::ignoreRoutes();

// ゲストユーザー向けのルート (認証不要)
Route::middleware(['guest'])->group(function () {
    // 1. 会員登録 Step 1 (GET /register/step1)
    Route::get('/register/step1', function () {
        return view('auth.register-step1');
    })->name('register.step1');

    // Fortifyの登録POSTルート (POST /register)
    Route::post('/register', [AuthController::class, 'storeStep1'])
        ->middleware('throttle:' . config('fortify.limiters.register'))
        ->name('register.post');

    // 2. 初期目標体重登録画面 Step 2 (/register/step2)
    Route::get('/register/step2', [AuthController::class, 'createStep2'])->name('register.step2');
    Route::post('/register/step2/complete', [AuthController::class, 'storeStep2'])->name('register.step2.store');

    // 3. ログイン画面 (GET /login)
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // FortifyのログインPOSTルート (POST /login)
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'throttle:' . config('fortify.limiters.login'),
        ]))
        ->name('login.post');
});

// ログイン後のルート (認証済みユーザー専用)
Route::middleware(['auth'])->group(function () {

    // ログアウトルート (404対策済み)
    Route::post('/logout', [LogoutRedirectController::class, 'destroy'])->name('logout');

    // 体重管理画面 (WeightLogControllerを使用)

    // FN016: ダッシュボード表示
    Route::get('/weight-logs', [WeightLogController::class, 'index'])->name('weight-logs');
    // FN023-1: 登録処理
    Route::post('/weight-logs', [WeightLogController::class, 'store'])->name('store_log');

    // FN030: 目標体重設定画面表示
    Route::get('/weight-logs/goal_setting', [WeightLogController::class, 'goalSetting'])->name('goal_setting');
    // FN034-1: 目標体重更新処理
    Route::put('/weight-logs/goal', [WeightLogController::class, 'updateGoal'])->name('update_goal');

    // FN024: 詳細(情報更新)画面表示
    Route::get('/weight-logs/{weightLogId}/update', [WeightLogController::class, 'edit'])->name('edit_log');
    // FN029-1: 情報更新処理
    Route::put('/weight-logs/{weightLogId}', [WeightLogController::class, 'update'])->name('update_log');
    // FN028: 削除処理
    Route::delete('/weight-logs/{weightLogId}', [WeightLogController::class, 'destroy'])->name('delete_log');
});
