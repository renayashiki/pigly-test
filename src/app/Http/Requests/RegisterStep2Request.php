<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStep2Request extends FormRequest
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
            'current_weight' => [
                'required',
                'numeric',
                'max:999.9',
                'regex:/^\d+(\.\d{1})?$/',
            ],
            'target_weight' => [
                'required',
                'numeric',
                'max:999.9',
                'regex:/^\d+(\.\d{1})?$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_weight.required' => '体重を入力してください',
            'target_weight.required' => '体重を入力してください',
            'current_weight.numeric' => '数字で入力してください',
            'target_weight.numeric' => '数字で入力してください',
            'current_weight.max' => '4桁までの数字で入力してください',
            'target_weight.max' => '4桁までの数字で入力してください',
            'current_weight.regex' => '小数点は1桁で入力してください',
            'target_weight.regex' => '小数点は1桁で入力してください',
        ];
    }
}
