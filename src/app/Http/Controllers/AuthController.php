<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterStep1Request; // Step 1のリクエストをインポート
use App\Http\Requests\RegisterStep2Request; // Step 2のリクエストをインポート
use App\Actions\Fortify\CreateNewUser; // CreateNewUserアクションをインポート
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Step 1のデータを受け取り、バリデーション後にCreateNewUserアクションを実行します。
     */
    public function storeStep1(RegisterStep1Request $request)
    {
        // RegisterStep1Requestのバリデーションが成功した後に、CreateNewUserアクションを実行します。
        // CreateNewUserアクション内で、Step 1のデータがセッションに保存され、Step 2へリダイレクトされます。

        // FortifyのCreateNewUserロジックを実行
        // ここでHttpResponseException（リダイレクト）が発生し、Step 2へ遷移します。
        // Note: Fortifyはrequest objectではなく、$request->all() (array)を受け取る想定です。
        app(CreatesNewUsers::class)->create($request->all());

        // ここには到達しないはずですが、念のため
        return redirect()->route('register.step2');
    }

    /**
     * Step 2のフォームを表示します。
     */
    public function createStep2()
    {
        // Step 1のデータがセッションにない場合、Step 1へ戻す
        if (!Session::get('register_step1_data')) {
            return redirect()->route('register.step1');
        }
        return view('auth.register-step2');
    }

    /**
     * Step 2のデータを受け取り、バリデーション後にCreateNewUserアクションを再実行してユーザーを登録します。
     */
    public function storeStep2(RegisterStep2Request $request) // ★ RegisterStep2Request を使用
    {
        // RegisterStep2Requestのバリデーションが成功した後に、CreateNewUserアクションを実行します。
        // CreateNewUser内でStep 1とStep 2のデータをマージしてDBに保存し、ログイン処理まで行われます。

        $step1_data = Session::get('register_step1_data');

        if (!$step1_data) {
            // セッション切れ対策
            return redirect()->route('register.step1')->withErrors(['error' => '登録プロセスが中断されました。最初からやり直してください。']);
        }

        // FortifyのRegisteredUserController::store()の処理を再現
        $user = app(CreatesNewUsers::class)->create(array_merge(
            $step1_data,
            $request->all()
        ));

        // セッションデータをクリア（CreateNewUser内でクリアされていない場合を考慮）
        Session::forget('register_step1_data');

        // ユーザーをログインさせ、リダイレクト
        Auth::login($user);

        return redirect()->route('weight-logs'); // 登録完了後のリダイレクト先
    }
}
