<?php

use Illuminate\Support\Facades\Route;
// AuthController を使用するように修正
use App\Http\Controllers\AuthController;
// Fortifyのデフォルトコントローラー
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ゲストユーザー向けのルート (認証不要)
Route::middleware(['guest'])->group(function () {
    // 1. 会員登録 Step 1 (GET /register/step1)
    // ご要望のパス /register/step1 に設定。名前はFortifyのリンクを活かすために 'register' を維持。
    Route::get('/register/step1', function () {
        // FortifyServiceProvider.php でこのルートにアクセスがあった際、auth.register-step1 を表示するように設定済み
        return view('auth.register-step1');
    })->name('register'); // Fortifyがリンクを張るために必要な名前

    // Fortifyの登録POSTルート (POST /register)
    // POST処理のパスはFortifyのデフォルトパスを維持。名前を register.post とし、Step 1のフォームアクションで参照します。
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:' . config('fortify.limiters.register'))
        ->name('register.post'); // POST用の名前

    // 2. 初期目標体重登録画面 Step 2 (/register/step2)
    // AuthControllerの createStep2 / storeStep2 メソッドを使用
    Route::get('/register/step2', [AuthController::class, 'createStep2'])->name('register.step2');
    Route::post('/register/step2', [AuthController::class, 'storeStep2'])->name('register.step2.store');

    // 3. ログイン画面 (/login)
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login'); // Fortifyが参照する名前付きルート

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
    // パスは /weight-logs
    Route::get('/weight-logs', function () {
        // ここに体重管理画面のビュー（ダッシュボード）を表示
        return view('dashboard');
    })->name('weight-logs');
});
