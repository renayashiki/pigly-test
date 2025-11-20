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
        // 目標体重をランダムに設定（50.0kg～70.0kgの間）
        $targetWeight = $this->faker->randomFloat(1, 50, 70);

        return [
            'target_weight' => $targetWeight,
        ];
    }
}
