<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WeightTarget;

class EnsureGoalSettingComplete
{
    /**
     * リクエストを処理する
     */
    public function handle(Request $request, Closure $next)
    {
        // ここに到達するユーザーは「認証済み」が前提。

        $userId = Auth::id();
        // WeightTargetのデータが存在しないことを「初期設定が未完了」と定義
        $hasTarget = WeightTarget::where('user_id', $userId)->exists();

        // 目標設定データがない場合 (Step 2 未完了の場合)、/weight_logs には進ませず Step 2 へリダイレクト
        if (!$hasTarget) {
            // 認証済みユーザーを Step 2 のルートにリダイレクト
            return redirect(route('register.step2'));
        }

        // チェックを通過した場合、次の処理へ進む
        return $next($request);
    }
}
