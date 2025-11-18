<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalSettingRequest;
use App\Models\WeightTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\WeightLogRequest; // 更新処理でバリデーションを使用
use App\Models\WeightLog;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
    public function store(WeightLogRequest $request): RedirectResponse
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
    public function goalSetting(): View
    {
        // 既存の目標体重を取得し、ビューに渡す
        $weightTarget = WeightTarget::where('user_id', Auth::id())->first();
        $targetWeight = $weightTarget ? $weightTarget->target_weight : null;

        return view('admin.goal_setting', compact('targetWeight'));
    }

    /**
     * FN034-1: 目標体重の更新処理
     */
    public function updateGoal(GoalSettingRequest $request): RedirectResponse
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
     *
     * @param int $weightLogId URLから渡されたログID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($weightLogId)
    {
        // 該当IDのログデータを取得するロジックを実装（FN024）
        try {
            // ログIDがユーザーのものか確認しつつ取得 (セキュリティ対策)
            $logData = WeightLog::where('user_id', Auth::id())
                ->findOrFail($weightLogId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ログが見つからない、または他のユーザーのIDだった場合
            Log::warning('不正なログIDアクセスまたはログ未発見: UserID=' . Auth::id() . ', LogID=' . $weightLogId);
            return redirect()->route('weight-logs')->with('error', '指定されたログが見つかりませんでした。');
        }

        // 取得したログデータ ($logData) とIDをViewに渡す
        return view('admin.update_log', [
            'weightLogId' => $weightLogId,
            'logData' => $logData, // フォームの初期値として使用
        ]);
    }

    /**
     * FN029-1: 情報更新処理
     *
     * @param WeightLogRequest $request バリデーション済みのリクエスト
     * @param int $weightLogId 更新対象のログID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(WeightLogRequest $request, $weightLogId): RedirectResponse
    {
        // WeightLogRequestを使用して、バリデーションとデータ取得を行う
        $validatedData = $request->validated();

        try {
            // 該当ログを取得（ユーザーIDもチェックしてセキュリティを担保）
            $log = WeightLog::where('user_id', Auth::id())
                ->findOrFail($weightLogId);

            // データの更新
            $log->update([
                'date' => $validatedData['date'],
                'weight' => $validatedData['weight'],
                'calories' => $validatedData['calories'],
                'exercise_time' => $validatedData['exercise_time'],
                'exercise_content' => $validatedData['exercise_content'] ?? null,
            ]);

            return redirect()->route('weight-logs')->with('success', '体重ログを更新しました。');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ログが見つからない、または他のユーザーのIDだった場合
            Log::warning('更新対象のログ未発見: UserID=' . Auth::id() . ', LogID=' . $weightLogId);
            return redirect()->route('weight-logs')->with('error', '更新対象のログが見つかりませんでした。');
        } catch (\Exception $e) {
            Log::error('体重ログの更新エラー: ' . $e->getMessage());
            // エラー時は、入力値を保持したまま更新画面に戻る
            return redirect()->back()->withInput()->with('error', '体重ログの更新中にエラーが発生しました。');
        }
    }

    /**
     * FN028: データ削除処理
     *
     * @param int $weightLogId 削除対象のログID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($weightLogId): RedirectResponse
    {
        try {
            // FN028: 該当IDのログデータを削除する処理を実装
            $deletedCount = WeightLog::where('user_id', Auth::id())
                ->where('id', $weightLogId)
                ->delete();

            if ($deletedCount > 0) {
                return redirect()->route('weight-logs')->with('success', '体重ログを削除しました。');
            } else {
                // ログが見つからないか、他のユーザーのログだった場合
                Log::warning('削除対象のログ未発見: UserID=' . Auth::id() . ', LogID=' . $weightLogId);
                return redirect()->route('weight-logs')->with('error', '削除対象のログが見つかりませんでした。');
            }
        } catch (\Exception $e) {
            Log::error('体重ログの削除エラー: ' . $e->getMessage());
            return redirect()->route('weight-logs')->with('error', '体重ログの削除中にエラーが発生しました。');
        }
    }
}
