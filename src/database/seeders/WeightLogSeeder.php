<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('weight_logs')->truncate();
        DB::table('weight_targets')->truncate();

        // UserSeederで作成されたテストユーザーを取得
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            echo "テストユーザーが見つかりませんでした。UserSeederが実行されているか確認してください。\n";
            return;
        }

        $userId = $user->id;

        // 目標体重のダミーデータを1件作成
        WeightTarget::factory()->create([
            'user_id' => $userId,
            'target_weight' => 65.0,
        ]);

        // 体重ログのダミーデータを35件作成
        WeightLog::factory()->count(35)->create([
            'user_id' => $userId,
        ]);
    }
}
