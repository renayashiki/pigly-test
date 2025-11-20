<?php

namespace App\Http\Controllers;

use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterStep1Request;
use App\Http\Requests\RegisterStep2Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Step 1のデータを受け取り、バリデーション後にCreateNewUserアクションを実行
    public function storeStep1(RegisterStep1Request $request)
    {
        app(CreatesNewUsers::class)->create($request->all());
        return redirect()->route('register.step2');
    }

    // Step2のフォームを表示
    public function createStep2()
    {
        if (!Session::get('register_step1_data')) {
            return redirect()->route('register.step1');
        }
        return view('auth.register-step2');
    }


    // Step2のデータを受け取り、バリデーション後にCreateNewUserアクションを再実行してユーザーを登録
    public function storeStep2(RegisterStep2Request $request)
    {
        $step1_data = Session::get('register_step1_data');
        if (!$step1_data) {
            return redirect()->route('register.step1')->withErrors(['error' => '登録プロセスが中断されました。最初からやり直してください。']);
        }

        // FortifyのRegisteredUserController::store()の処理を再現
        $user = app(CreatesNewUsers::class)->create(array_merge(
            $step1_data,
            $request->all()
        ));

        // セッションデータをクリア
        Session::forget('register_step1_data');
        Auth::login($user);
        return redirect()->route('weight-logs');
    }
}
