<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeightLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のログを全て削除（テスト用）
        DB::table('weight_logs')->truncate();

        $faker = \Faker\Factory::create('ja_JP');
        $logs = [];
        $startDate = Carbon::now()->subYear(); // 1年前からスタート

        // 30件のダミーデータを作成 (件数を30に変更)
        for ($i = 0; $i < 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i * 7); // 7日ごとに記録（週に1回程度のイメージ）

            // 体重 (例: 50.0kg から 80.0kg の範囲でランダムに生成)
            $weight = $faker->randomFloat(1, 50, 80);

            // 食事摂取カロリー (例: 1500kcal から 3000kcal の範囲)
            $calories = $faker->numberBetween(1500, 3000);

            // 運動時間 (例: 00:00 から 02:00 の範囲)
            $exerciseHour = $faker->numberBetween(0, 2);
            $exerciseMinute = $faker->numberBetween(0, 59);
            $exerciseTime = sprintf('%02d:%02d:00', $exerciseHour, $exerciseMinute);

            // 運動内容
            $exerciseContent = $faker->randomElement([
                'ウォーキング 30分',
                'ジムで筋トレと有酸素運動',
                'ヨガとストレッチ',
                'ランニング 5km',
                '水泳 1時間'
            ]);

            $logs[] = [
                'user_id' => 1, // ログインユーザーのIDに合わせて調整が必要な場合があります
                'date' => $currentDate->format('Y-m-d'),
                'weight' => $weight,
                'calories' => $calories,
                'exercise_time' => $exerciseTime,
                'exercise_content' => $exerciseContent,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // データベースに挿入
        DB::table('weight_logs')->insert($logs);
    }
}
