<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalSettingRequest;
use App\Models\WeightTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\WeightLogRequest;
use App\Models\WeightLog;


class WeightLogController extends Controller
{
    /**
     * FN016: 管理画面(ダッシュボード)の表示
     */
    public function index()
    {
        $userId = Auth::id();

        // ユーザーの最新の目標体重を取得 (FN016-6)
        $weightTarget = WeightTarget::where('user_id', $userId)->first();
        $targetWeight = $weightTarget ? $weightTarget->target_weight : null;

        // DBからログ一覧を取得（FN016, ページネーション含む）
        // ページネーション：8件ごと (FN016 ページネーションの要件)
        $weightLogsQuery = WeightLog::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        $weightLogs = $weightLogsQuery->paginate(8);

        return view('admin.dashboard', compact('targetWeight', 'weightLogs'));
    }
    /**
     * FN023-1: 体重ログの登録処理 (モーダル内)
     */
    public function store(WeightLogRequest $request)
    {
        $validatedData = $request->validated();
        $userId = Auth::id();

        try {
            // DB保存
            WeightLog::create([
                'user_id' => $userId,
                'date' => $validatedData['date'],
                'weight' => $validatedData['weight'],
                'calories' => $validatedData['calories'],
                'exercise_time' => $validatedData['exercise_time'],
                'exercise_content' => $validatedData['exercise_content'] ?? null,
            ]);

            return redirect()->route('weight-logs')->with('success', '体重ログを登録しました。');
        } catch (\Exception $e) {
            Log::error('体重ログの登録エラー: ' . $e->getMessage());

            return redirect()->route('weight-logs')->with('error', '体重ログの登録中にエラーが発生しました。再度お試しください。');
        }
    }

    /**
     * FN030: 目標体重設定画面の表示
     */
    public function goalSetting()
    {
        // 既存の目標体重を取得し、ビューに渡す
        $weightTarget = WeightTarget::where('user_id', Auth::id())->first();
        $targetWeight = $weightTarget ? $weightTarget->target_weight : null;

        return view('admin.goal_setting', compact('targetWeight'));
    }

    /**
     * FN034-1: 目標体重の更新処理
     */
    public function updateGoal(GoalSettingRequest $request)
    {
        $validatedData = $request->validated();

        try {
            WeightTarget::updateOrCreate(
                ['user_id' => Auth::id()],
                ['target_weight' => $validatedData['target_weight']]
            );

            return redirect()->route('weight-logs')->with('success', '目標体重を更新しました。');
        } catch (\Exception $e) {
            Log::error('目標体重の保存エラー: ' . $e->getMessage());

            return redirect()->back()->with('error', '目標体重の保存中にエラーが発生しました。時間をおいて再度お試しください。');
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
        // TODO: ここに更新ロジックの実装が必要です
        return redirect()->route('weight-logs')->with('success', '体重ログを更新しました。');
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
