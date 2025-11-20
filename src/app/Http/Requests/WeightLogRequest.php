<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightLogRequest extends FormRequest
{
    /**
     * リクエストがこのバリデーションを通過できるか決定します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * リクエストに適用されるバリデーションルールを取得します。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            // 体重: 必須、数値、min/maxで4桁以内を担保
            // decimal:0,1 の代わりに、小数点以下1桁または整数を許可する正規表現を使用
            'weight' => 'required|numeric|max:999.9|regex:/^\d+(\.\d{1})?$/',
            // カロリー: 必須、整数
            'calories' => 'required|numeric|min:0|max:10000',
            // 運動時間: 必須
            'exercise_time' => 'required|date_format:H:i',
            // 運動内容: 任意、最大120文字
            'exercise_content' => 'nullable|string|max:120',
        ];
    }

    /**
     * バリデーションエラーメッセージを取得します。
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // 日付 (FN027-1a)
            'date.required' => '日付を入力してください',

            // 体重 (FN027-2)
            'weight.required' => '体重を入力してください',
            'weight.numeric' => '数字で入力してください',
            'weight.max' => '4桁までの数字で入力してください',
            // decimal の代わりに regex エラーメッセージを使用
            'weight.regex' => '小数点は1桁までで入力してください',

            // 摂取カロリー (FN027-3)
            'calories.required' => '摂取カロリーを入力してください',
            'calories.numeric' => '数字で入力してください',

            // 運動時間 (FN027-4a)
            'exercise_time.required' => '運動時間を入力してください',

            // 運動内容 (FN027-5a)
            'exercise_content.max' => '120文字以内で入力してください',
        ];
    }
}
