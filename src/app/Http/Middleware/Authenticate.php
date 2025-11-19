<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // 未認証の場合のリダイレクト先を 'login' から 'register' ルートへ変更
            // Fortifyの登録画面（Step 1のビュー）が表示されるルートです。
            return route('register');
        }
    }
}
