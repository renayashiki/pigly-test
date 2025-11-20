<?php

namespace Database\Factories;

use App\Models\WeightTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeightTargetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeightTarget::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 目標体重をランダムに設定（例: 50.0kgから70.0kgの間）
        $targetWeight = $this->faker->randomFloat(1, 50, 70);

        return [
            // user_id はシーダー実行時に指定します
            'target_weight' => $targetWeight,
        ];
    }
}
