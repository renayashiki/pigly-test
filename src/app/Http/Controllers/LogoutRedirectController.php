<?php

namespace App\Http\Controllers;

// 必須インポート
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
// 親クラスのインポート
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class LogoutRedirectController extends AuthenticatedSessionController
{
    // 注意: 親クラスの依存関係を使用しないため、
    // コンストラクタ (__construct) の定義を削除し、IDEの継承エラーを回避します。
    // 親クラスの依存関係はLaravelのIoCコンテナが解決します。

    /**
     * ログアウト処理を実行し、カスタムリダイレクトに移行します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. 認証ガードからのログアウト処理を実行
        Auth::guard(config('fortify.guard'))->logout();

        // 2. セッションの再生成とトークンの無効化
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. カスタムリダイレクトメソッド (loggedOut) を呼び出す
        return $this->loggedOut($request);
    }

    /**
     * ログアウト処理後に実行されるメソッドをオーバーライドし、ログアウト後のリダイレクト先を /login に強制する。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function loggedOut(Request $request): RedirectResponse
    {
        // ログアウト処理完了後、ログイン画面へリダイレクト
        return redirect()->route('login');
    }
}
