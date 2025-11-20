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
        return auth()->check();;
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
            'target_weight.required' => '体重を入力してください',
            'target_weight.numeric' => '数字で入力してください',
            'target_weight.max' => '4桁までの数字で入力してください',
            'target_weight.regex' => '小数点は1桁で入力してください',
        ];
    }
}
