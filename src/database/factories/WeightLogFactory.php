<?php

namespace Database\Factories;

use App\Models\WeightLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class WeightLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeightLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 過去90日間の間でランダムな日付を生成
        $pastDate = Carbon::today()->subDays(90);
        $currentDate = $this->faker->dateTimeBetween($pastDate, Carbon::today())->format('Y-m-d');

        // 運動時間
        $exerciseHour = $this->faker->numberBetween(0, 2);
        $exerciseMinute = $this->faker->numberBetween(0, 59);
        $exerciseTime = sprintf('%02d:%02d:00', $exerciseHour, $exerciseMinute);

        return [
            // user_id はシーダー実行時に指定します
            'date' => $currentDate,
            // 体重
            'weight' => $this->faker->randomFloat(1, 50, 80),
            // 食事摂取カロリー
            'calories' => $this->faker->numberBetween(1500, 3000),
            'exercise_time' => $exerciseTime,
            'exercise_content' => $this->faker->randomElement([
                'ウォーキング 30分',
                'ジムで筋トレと有酸素運動',
                'ヨガとストレッチ',
                'ランニング 5km',
                '水泳 1時間',
                '特になし'
            ]),
        ];
    }
}
