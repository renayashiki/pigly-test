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
     * Fortifyの登録処理をカスタマイズし、多段階登録に対応させます。
     *
     * @param  array<string, string>  $input
     * @return \App\Models\User|void
     */
    public function create(array $input)
    {
        // -------------------------------------------
        // Step 1のデータが送られてきた場合 (name, email, password)
        // -------------------------------------------
        if (isset($input['name']) && isset($input['email']) && isset($input['password']) && !isset($input['current_weight'])) {

            // Step 1: バリデーションとセッション保存
            $validator = Validator::make($input, [
                // バリデーション: お名前入力必須、文字列
                'name' => ['required', 'string', 'max:255'],
                // バリデーション: メールアドレス入力必須、メール形式、ユニーク
                'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
                // バリデーション: パスワード入力必須
                'password' => ['required',],
            ], [
                // 1. お名前
                'name.required' => 'お名前を入力してください',

                // 2. メールアドレス
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',

                // 3. パスワード
                'password.required' => 'パスワードを入力してくだい',
            ]);

            $validator->validate();

            // ユーザー情報（お名前、メールアドレス、ハッシュ化されたパスワード）をセッションに保存
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

        // -------------------------------------------
        // Step 2のデータが送られてきた場合 (current_weight, target_weight)
        // -------------------------------------------
        if (isset($input['current_weight']) && isset($input['target_weight'])) {

            $step1_data = Session::get('register_step1_data');

            // セッションデータがない場合はStep 1へリダイレクト (防御的チェック)
            if (!$step1_data) {
                throw new HttpResponseException(
                    redirect()->route('register')
                );
            }

            // Step 2 バリデーション
            // ルール: 必須, 数値型, 整数部最大4桁かつ小数点1桁までを許可する正規表現
            $rules = [
                'current_weight' => ['required', 'numeric', 'regex:/^\d{1,4}(\.\d{1})?$/'],
                'target_weight' => ['required', 'numeric', 'regex:/^\d{1,4}(\.\d{1})?$/'],
            ];

            $messages = [
                // a. 未入力の場合 -> required
                'current_weight.required' => '体重を入力してください',
                'target_weight.required' => '体重を入力してください',

                // b. 数値じゃない場合 -> numeric (デフォルト)
                'current_weight.numeric' => '数字で入力してください',
                'target_weight.numeric' => '数字で入力してください',

                // d. 小数点が1桁じゃない場合 -> regex
                'current_weight.regex' => '小数点は1桁で入力してください',
                'target_weight.regex' => '小数点は1桁で入力してください',
            ];

            // PHPDocにより、IDEに$validatorが具象クラスであることを明示し、赤線を回避します。
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = Validator::make($input, $rules, $messages);

            // c. 数値が4桁以内じゃない場合（整数部が5桁以上の場合）のエラーメッセージを上書き
            $validator->addReplacer('numeric', function ($message, $attribute, $rule, $parameters) use ($input) {
                if ($rule === 'Numeric' && isset($input[$attribute])) {
                    $value = (string)$input[$attribute];

                    // 整数部が5桁以上の場合 (例: 10000, 10000.1など)
                    // このケースは本来 numeric ルールでのデフォルトメッセージ(b)になるが、
                    // ここでメッセージを上書きし (c) を適用する。
                    if (is_numeric($input[$attribute]) && preg_match('/^\d{5,}(\.\d*)?$/', $value)) {
                        return '4桁までの数字で入力してください'; // 1.c/2.c
                    }
                }

                // それ以外（純粋な非数値など）は元のメッセージ（b）を使用
                return $message;
            });

            $validator->validate();

            // ユーザーをデータベースに登録
            return DB::transaction(function () use ($step1_data, $input) {
                // Userモデルにcurrent_weight, target_weightカラムが追加されていることが前提
                $user = User::create(array_merge($step1_data, [
                    'current_weight' => $input['current_weight'],
                    'target_weight' => $input['target_weight'],
                ]));

                // セッションデータをクリア
                Session::forget('register_step1_data');

                return $user; // Fortifyが自動的にログイン処理を行う
            });
        }
    }
}
