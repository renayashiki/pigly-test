<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LogoutResponse; // FortifyのLogoutResponseコントラクト

class LogoutRedirectController extends Controller
{
    /**
     * @var \Laravel\Fortify\Contracts\LogoutResponse
     */
    protected $logoutResponse;

    /**
     * LogoutResponseコントラクトを注入します。
     * LaravelのIoCコンテナが自動で解決します。
     *
     * @param  \Laravel\Fortify\Contracts\LogoutResponse  $logoutResponse
     * @return void
     */
    public function __construct(LogoutResponse $logoutResponse)
    {
        $this->logoutResponse = $logoutResponse;
    }

    /**
     * ログアウト処理を実行し、カスタムリダイレクトに移行します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request)
    {
        // Fortifyの標準ログアウト処理
        Auth::guard(config('fortify.guard'))->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ここでFortifyのLogoutResponseの代わりに、カスタムのRedirectResponseを返す
        return redirect()->route('login');
    }
}
