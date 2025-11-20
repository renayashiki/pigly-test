<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightLogRequest extends FormRequest
{
    /*
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /*
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'weight' => 'required|numeric|max:999.9|regex:/^\d+(\.\d{1})?$/',
            'calories' => 'required|numeric|min:0|max:10000',
            'exercise_time' => 'required|date_format:H:i',
            'exercise_content' => 'nullable|string|max:120',
        ];
    }

    /*
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'date.required' => '日付を入力してください',
            'weight.required' => '体重を入力してください',
            'weight.numeric' => '数字で入力してください',
            'weight.max' => '4桁までの数字で入力してください',
            'weight.regex' => '小数点は1桁までで入力してください',
            'calories.required' => '摂取カロリーを入力してください',
            'calories.numeric' => '数字で入力してください',
            'exercise_time.required' => '運動時間を入力してください',
            'exercise_content.max' => '120文字以内で入力してください',
        ];
    }
}
