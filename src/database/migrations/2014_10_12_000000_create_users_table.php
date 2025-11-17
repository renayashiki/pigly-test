<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            // --- カスタム追加カラム ---
            $table->decimal('current_weight', 4, 1)->nullable()->comment('現在の体重');
            $table->decimal('target_weight', 4, 1)->nullable()->comment('目標体重');
            $table->boolean('is_admin')->default(false)->comment('管理者フラグ');
            // -------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
