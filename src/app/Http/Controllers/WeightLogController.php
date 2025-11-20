<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalSettingRequest;
use App\Models\WeightTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\WeightLogRequest;
use App\Models\WeightLog;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class WeightLogController extends Controller
{
    // 管理画面の表示
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ユーザーの最新の目標体重を取得
        $weightTarget = WeightTarget::where('user_id', $userId)->first();
        $targetWeight = $weightTarget ? $weightTarget->target_weight : null;

        // DBからログ一覧を取得
        $weightLogsQuery = WeightLog::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        // 検索条件の適用
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($dateFrom) {
            $weightLogsQuery->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $weightLogsQuery->where('date', '<=', $dateTo);
        }

        $weightLogs = $weightLogsQuery->paginate(8);

        return view('admin.dashboard', compact('targetWeight', 'weightLogs'));
    }

    // 体重ログの登録処理 (モーダル内)
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
        } catch (Exception $e) {
            // エラー捕捉
            Log::error('体重ログの登録エラー: ' . $e->getMessage());
            return redirect()->route('weight-logs')->with('error', '体重ログの登録中にエラーが発生しました。再度お試しください。');
        }
    }

    // 目標体重設定画面の表示
    public function goalSetting(): View
    {
        // 既存の目標体重を取得し、ビューに渡す
        $weightTarget = WeightTarget::where('user_id', Auth::id())->first();
        $targetWeight = $weightTarget ? $weightTarget->target_weight : null;

        return view('admin.goal_setting', compact('targetWeight'));
    }

    // 目標体重の更新処理
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

    // 詳細画面の表示
    public function edit($weightLogId)
    {
        // 該当IDのログデータを取得するロジック
        try {
            $logData = WeightLog::where('user_id', Auth::id())
                ->findOrFail($weightLogId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ログが見つからない、または他のユーザーのIDだった場合
            Log::warning('不正なログIDアクセスまたはログ未発見: UserID=' . Auth::id() . ', LogID=' . $weightLogId);
            return redirect()->route('weight-logs')->with('error', '指定されたログが見つかりませんでした。');
        }

        // 取得したログデータをViewに渡す
        return view('admin.update_log', [
            'weightLogId' => $weightLogId,
            'logData' => $logData,
        ]);
    }

    // 情報更新処理
    public function update(WeightLogRequest $request, $weightLogId): RedirectResponse
    {
        // WeightLogRequestを使用しバリデーションとデータ取得を行う
        $validatedData = $request->validated();

        try {
            // 該当ログを取得
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
            return redirect()->back()->withInput()->with('error', '体重ログの更新中にエラーが発生しました。');
        }
    }

    // データ削除処理
    public function destroy($weightLogId): RedirectResponse
    {
        try {
            //該当IDのログデータを削除する処理
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
