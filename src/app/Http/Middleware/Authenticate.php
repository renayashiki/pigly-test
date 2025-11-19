<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

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
            // ★ 修正箇所：未認証の場合、ログイン画面ではなく /register/step1 にリダイレクトする
            // ★ これで、未認証アクセス時も要件が満たされます。
            return route('register.step1');
        }
    }
}
