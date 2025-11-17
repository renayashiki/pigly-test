<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class LogoutRedirectController extends AuthenticatedSessionController
{
    /**
     * ログアウト処理後に実行されるメソッドをオーバーライドしログアウト後のリダイレクト先を /login に強制する。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function loggedOut(Request $request)
    {
        // ログアウト処理完了後、ログイン画面へリダイレクト
        return redirect()->route('login');
    }
}
