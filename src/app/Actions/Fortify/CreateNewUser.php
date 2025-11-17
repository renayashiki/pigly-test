<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

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

            // Step 1: バリデーションとセッション保存 (FN003, FN004)
            $validator = Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
                'password' => $this->passwordRules(),
            ], [
                'name.required' => 'お名前を入力してください',
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
                'email.unique' => 'このメールアドレスは既に使用されています。',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
            ]);

            $validator->validate();

            // ユーザー情報（お名前、メールアドレス、ハッシュ化されたパスワード）をセッションに保存 (FN005)
            Session::put('register_step1_data', [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // Fortifyの挙動を停止し、Step 2へリダイレクトするための例外をスロー
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

            // Step 2 バリデーション (FN008, FN009)
            // 体重の「4桁以内」「小数点1桁」を考慮し、ここでは「整数部最大3桁かつ小数点1桁まで」の形式を正規表現でチェック
            $validator = Validator::make($input, [
                'current_weight' => ['required', 'numeric', 'regex:/^\d{1,3}(\.\d{1})?$/'],
                'target_weight' => ['required', 'numeric', 'regex:/^\d{1,3}(\.\d{1})?$/'],
            ], [
                'current_weight.required' => '体重を入力してください',
                'current_weight.numeric' => '数字で入力してください',
                'current_weight.regex' => '4桁までの数字で入力してください', // FN009c, FN009dを兼ねる
                'target_weight.required' => '体重を入力してください',
                'target_weight.numeric' => '数字で入力してください',
                'target_weight.regex' => '4桁までの数字で入力してください', // FN009c, FN009dを兼ねる
            ]);

            $validator->validate();

            // ユーザーをデータベースに登録 (FN005, FN010)
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
