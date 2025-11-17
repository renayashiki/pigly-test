<?php

use Illuminate\Support\Facades\Route;
// AuthController を使用
use App\Http\Controllers\AuthController;
// Fortifyのデフォルトコントローラー
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\LogoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ゲストユーザー向けのルート (認証不要)
Route::middleware(['guest'])->group(function () {
    // 1. 会員登録 Step 1 (GET /register/step1)
    Route::get('/register/step1', function () {
        return view('auth.register-step1');
    })->name('register');

    // Fortifyの登録POSTルート (POST /register)
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:' . config('fortify.limiters.register'))
        ->name('register.post');

    // 2. 初期目標体重登録画面 Step 2 (/register/step2)
    Route::get('/register/step2', [AuthController::class, 'createStep2'])->name('register.step2');
    Route::post('/register/step2', [AuthController::class, 'storeStep2'])->name('register.step2.store');

    // 3. ログイン画面 (/login)
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // FortifyのログインPOSTルート
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'throttle:' . config('fortify.limiters.login'),
        ]))
        ->name('login');
});

// ログイン後のルート (認証済みユーザー専用)
Route::middleware(['auth'])->group(function () {
    // ログイン、またはStep 2完了後の遷移先 (体重管理画面)
    // ビューパスは 'admin.dashboard'
    Route::get('/weight-logs', function () {
        return view('admin.dashboard');
    })->name('weight-logs');

    // ログアウトルート
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');
});
