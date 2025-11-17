<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\ValidationException;

// AuthControllerとして、多段階認証に関連するカスタム処理を一元管理します。
class AuthController extends Controller
{
    /**
     * Step 2 画面表示 (/register/step2)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createStep2(Request $request)
    {
        // Step 1のデータがない場合はStep 1へリダイレクト
        if (!Session::has('register_step1_data')) {
            return redirect()->route('register');
        }
        return view('auth.register-step2');
    }

    /**
     * Step 2 登録処理 (FortifyのCreateNewUserを呼び出す)
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Contracts\CreatesNewUsers  $creator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeStep2(Request $request, CreatesNewUsers $creator)
    {
        $input = $request->all();

        try {
            // Fortifyの登録処理を実行 (CreateNewUser::create()がStep 2として呼ばれる)
            $user = $creator->create($input);

            // 登録成功後、Fortifyが自動的にログインし、HOMEへリダイレクトする (/weight-managementを想定)
            return redirect()->intended(config('fortify.home'));
        } catch (ValidationException $e) {
            // バリデーションエラーをセッションに保存してStep 2に戻る
            return redirect()->route('register.step2')
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            // その他のエラー処理
            error_log('User registration error: ' . $e->getMessage());
            return redirect()->route('register.step2')->withInput()->withErrors(['general' => 'ユーザー登録中にエラーが発生しました。']);
        }
    }
}
