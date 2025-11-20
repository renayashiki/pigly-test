<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// 必要なモデルをuse
use App\Models\User;
use App\Models\WeightTarget;
use App\Models\WeightLog;

class WeightLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のログと目標を全て削除
        DB::table('weight_logs')->truncate();
        DB::table('weight_targets')->truncate();

        // UserSeederで作成されたテストユーザーを取得
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            echo "テストユーザーが見つかりませんでした。UserSeederが実行されているか確認してください。\n";
            return;
        }

        $userId = $user->id;

        // 目標体重のダミーデータを1件作成 (ファクトリを使用)
        WeightTarget::factory()->create([
            'user_id' => $userId,
            'target_weight' => 65.0, // テスト用の目標値
        ]);

        // 体重ログのダミーデータを35件作成 (ファクトリを使用)
        WeightLog::factory()->count(35)->create([
            'user_id' => $userId,
        ]);
    }
}
