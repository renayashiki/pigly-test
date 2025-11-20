<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateNewUser implements CreatesNewUsers
{
    /**
     *
     *
     * @param  array<string, string>  $input
     * @return \App\Models\User|void
     */
    public function create(array $input)
    {
        // Step 1のデータが送られてきた場合 (name, email, password)
        if (isset($input['name']) && isset($input['email']) && isset($input['password']) && !isset($input['current_weight'])) {

            // Step 1: バリデーションとセッション保存
            $validator = Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
                'password' => ['required',],
            ], [
                'name.required' => 'お名前を入力してください',
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
                'password.required' => 'パスワードを入力してくだい',
            ]);

            $validator->validate();

            // ユーザー新規登録情報をセッションに保存
            Session::put('register_step1_data', [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // Fortifyの挙動を停止し、Step 2へリダイレクト
            throw new HttpResponseException(
                redirect()->route('register.step2')
            );
        }

        // Step 2のデータが送られてきた場合
        if (isset($input['current_weight']) && isset($input['target_weight'])) {

            $step1_data = Session::get('register_step1_data');

            // セッションデータがない場合はStep 1へリダイレクト
            if (!$step1_data) {
                throw new HttpResponseException(
                    redirect()->route('register')
                );
            }

            // Step 2 バリデーション
            $rules = [
                'current_weight' => ['required', 'numeric', 'regex:/^\d{1,4}(\.\d{1})?$/'],
                'target_weight' => ['required', 'numeric', 'regex:/^\d{1,4}(\.\d{1})?$/'],
            ];

            $messages = [
                'current_weight.required' => '体重を入力してください',
                'target_weight.required' => '体重を入力してください',
                'current_weight.numeric' => '数字で入力してください',
                'target_weight.numeric' => '数字で入力してください',
                'current_weight.regex' => '小数点は1桁で入力してください',
                'target_weight.regex' => '小数点は1桁で入力してください',
            ];

            // PHPDocにより、IDEに$validatorが具象クラスであることを明示し、赤線を回避。
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = Validator::make($input, $rules, $messages);
            $validator->addReplacer('numeric', function ($message, $attribute, $rule, $parameters) use ($input) {
                if ($rule === 'Numeric' && isset($input[$attribute])) {
                    $value = (string)$input[$attribute];

                    if (is_numeric($input[$attribute]) && preg_match('/^\d{5,}(\.\d*)?$/', $value)) {
                        return '4桁までの数字で入力してください';
                    }
                }
                return $message;
            });

            $validator->validate();

            // ユーザーをデータベースに登録
            return DB::transaction(function () use ($step1_data, $input) {
                $user = User::create(array_merge($step1_data, [
                    'current_weight' => $input['current_weight'],
                    'target_weight' => $input['target_weight'],
                ]));

                // セッションデータをクリア
                Session::forget('register_step1_data');

                return $user;
            });
        }
    }
}
