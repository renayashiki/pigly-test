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
            // usersテーブルに存在しないフィールド（current_weight, target_weight）は削除
            'is_admin' => false,
        ]);
    }
}
