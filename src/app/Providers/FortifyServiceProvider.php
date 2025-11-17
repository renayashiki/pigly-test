<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ユーザー作成ロジックとして、カスタムした CreateNewUser アクションを使用
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録画面として、Step 1のビューを明示的に指定
        Fortify::registerView(function () {
            // 私たちが定義した Step 1 の Blade ファイルを参照
            return view('auth.register-step1');
        });

        // ログイン画面として、Loginのビューを指定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン試行のレートリミッター設定
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
