<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weight_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // date: date, NOT NULL (ユーザーIDとの複合UNIQUEを設定)
            $table->date('date')->comment('日付');
            $table->decimal('weight', 4, 1)->comment('体重');
            $table->integer('calories')->nullable()->comment('食事量 (カロリー)');
            $table->time('exercise_time')->nullable()->comment('運動時間');
            $table->text('exercise_content')->nullable()->comment('運動内容');
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
        Schema::dropIfExists('weight_logs');
    }
}
