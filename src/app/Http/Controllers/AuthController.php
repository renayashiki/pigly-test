<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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
            // Fortifyの登録処理を実行 (CreateNewUser::create()が呼ばれる)
            // この中でユーザーがDBに登録され、自動的にログインされる
            $user = $creator->create($input);

            // 登録成功後、RouteServiceProvider::HOME (現在 /weight-logs) へリダイレクト
            return redirect()->intended(config('fortify.home'));
        } catch (ValidationException $e) {
            // バリデーションエラー
            return redirect()->route('register.step2')
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            // その他のエラー
            Log::error('User registration error in AuthController: ' . $e->getMessage());
            return redirect()->route('register.step2')->withInput()->withErrors(['general' => 'ユーザー登録中に予期せぬエラーが発生しました。']);
        }
    }
}
