<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightLog extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'weight_logs';

    // マスアサインメント可能なカラム
    protected $fillable = [
        'user_id',
        'date',
        'weight',
        'calories',
        'exercise_time',
        'exercise_content',
    ];

    // キャスト設定
    protected $casts = [
        'date' => 'date',
        'weight' => 'decimal:1',
        // exercise_time は TIME 型で、データベースの値を文字列として保持します。
    ];

    /**
     * このログを持つユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
