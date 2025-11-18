<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'target_weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:999.9',
                'regex:/^\d+(\.\d{1})?$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // a. 未入力
            'target_weight.required' => '体重を入力してください',

            // b. 数値じゃない
            'target_weight.numeric' => '数字で入力してください',

            // c. 4桁以内じゃない -> max
            'target_weight.max' => '4桁までの数字で入力してください',

            // d. 小数点が1桁じゃない -> regex
            'target_weight.regex' => '小数点は1桁で入力してください',
        ];
    }
}
