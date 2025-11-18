<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalSettingRequest;
use App\Models\WeightTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class WeightLogController extends Controller
{
    /**
     * FN016: 管理画面(ダッシュボード)の表示
     */
    public function index()
    {
        // データの取得ロジック（FN016: ログ、目標体重、ページネーション）をここに実装
        return view('admin.dashboard');
    }

    /**
     * FN023-1: 体重ログの登録処理 (モーダル内)
     */
    public function store(Request $request)
    {
        // FN021, FN022: FormRequestバリデーションとDB保存処理を想定
        return redirect()->route('weight-logs')->with('status', '体重ログを登録しました。');
    }

    /**
     * FN030: 目標体重設定画面の表示
     */
    public function goalSetting()
    {
        return view('admin.goal_setting');
    }

    /**
     * FN034-1: 目標体重の更新処理
     */
    public function updateGoal(GoalSettingRequest $request)
    {
        // $request 変数を使用するため、「薄くなる」警告は解消
        $validatedData = $request->validated(); // 自動バリデーション後のデータを取得

        try {
            // updateOrCreate: user_idが一致するレコードがあれば更新、なければ新規作成
            WeightTarget::updateOrCreate(
                ['user_id' => Auth::id()], // 検索条件
                ['target_weight' => $validatedData['target_weight']] // 更新データ (モデルの $fillable と一致)
            );

            // 成功メッセージとともにリダイレクト
            return redirect()->route('weight-logs')->with('success', '目標体重を更新しました。');
        } catch (\Exception $e) {
            Log::error('目標体重の保存エラー: ' . $e->getMessage());
            // エラーが発生した場合、設定画面に戻し、エラーメッセージを表示
            return redirect()->back()->withErrors(['target_weight' => '目標体重の保存中にエラーが発生しました。時間をおいて再度お試しください。']);
        }
    }

    /**
     * FN024: 詳細(情報更新)画面の表示
     */
    public function edit($weightLogId)
    {
        // FN024: 該当IDのログデータを取得するロジックをここに実装
        return view('admin.update_log', compact('weightLogId'));
    }

    /**
     * FN029-1: 情報更新処理
     */
    public function update(Request $request, $weightLogId)
    {
        // FN026, FN027: FormRequestバリデーションとDB更新処理を想定
        return redirect()->route('weight-logs')->with('status', '体重ログを更新しました。');
    }

    /**
     * FN028: データ削除処理
     */
    public function destroy($weightLogId)
    {
        // FN028: 該当IDのログデータを削除する処理をここに実装
        return redirect()->route('weight-logs')->with('status', '体重ログを削除しました。');
    }
}
