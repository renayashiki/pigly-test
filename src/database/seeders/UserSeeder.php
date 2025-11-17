<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ログイン認証テスト用のダミーユーザーを作成
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            // Step 2 で登録される想定の初期体重データ (ダミー値)
            'current_weight' => 75.5,
            'target_weight' => 65.0,
            'is_admin' => false,
        ]);
    }
}
