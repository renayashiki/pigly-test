<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WeightTarget; // 目標体重モデル（初期設定完了の判断に使用）

class EnsureGoalSettingComplete
{
    /**
     * リクエストを処理する
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 認証は 'auth' ミドルウェアが先にチェック済みであることを前提とする

        $userId = Auth::id();
        // WeightTargetのデータが存在しないことを「初期設定が未完了」と定義
        $hasTarget = WeightTarget::where('user_id', $userId)->exists();

        // 目標設定データがない場合、/weight_logs には進ませず /register/step1 へリダイレクト
        if (!$hasTarget) {
            // 要件通り、/register/step1 へリダイレクト
            return redirect('/register/step1');
        }

        // チェックを通過した場合、次の処理へ進む
        return $next($request);
    }
}
