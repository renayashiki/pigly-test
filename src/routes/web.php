<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\LogoutRedirectController;
use App\Http\Controllers\WeightLogController;
use Laravel\Fortify\Fortify;


Fortify::ignoreRoutes();

// ゲストユーザー向けのルート (認証不要)
Route::middleware(['guest'])->group(function () {
    // 1. 会員登録 Step 1 (GET /register/step1)
    Route::get('/register/step1', function () {
        return view('auth.register-step1');
    })->name('register.step1');

    // Fortifyの登録
    Route::post('/register', [AuthController::class, 'storeStep1'])
        ->middleware('throttle:' . config('fortify.limiters.register'))
        ->name('register.post');

    //初期目標体重登録画面 Step 2 (/register/step2)
    Route::get('/register/step2', [AuthController::class, 'createStep2'])->name('register.step2');
    Route::post('/register/step2/complete', [AuthController::class, 'storeStep2'])->middleware('throttle:register')->name('register.step2.store');

    //ログイン画面
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Fortifyのログイン
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'throttle:' . config('fortify.limiters.login'),
        ]))
        ->name('login.post');
});

// ログイン後のルート (認証済みユーザー専用)
Route::middleware(['auth'])->group(function () {

    // ログアウト
    Route::post('/logout', [LogoutRedirectController::class, 'destroy'])->name('logout');

    // 体重管理画面
    Route::get('/weight_logs', [WeightLogController::class, 'index'])->name('weight-logs');

    // 検索
    Route::get('/weight_logs/search', [WeightLogController::class, 'index'])->name('search_logs');

    //登録処理
    Route::post('/weight_logs/create', [WeightLogController::class, 'store'])->name('store_log');

    //目標体重設定画面表示
    Route::get('/weight_logs/goal_setting', [WeightLogController::class, 'goalSetting'])->name('goal_setting');

    //目標体重更新処理
    Route::put('/weight_logs/goal', [WeightLogController::class, 'updateGoal'])->name('update_goal');

    //詳細画面表示
    Route::get('/weight_logs/{weightLogId}', [WeightLogController::class, 'edit'])->name('edit_log');

    //情報更新処理
    Route::put('/weight_logs/{weightLogId}/update', [WeightLogController::class, 'update'])->name('update_log');

    //削除処理
    Route::delete('/weight_logs/{weightLogId}/delete', [WeightLogController::class, 'destroy'])->name('delete_log');
});
