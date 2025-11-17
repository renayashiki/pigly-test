<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightTarget extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'weight_targets';

    // マスアサインメント可能なカラム
    protected $fillable = [
        'user_id',
        'target_weight',
    ];

    // キャスト設定
    protected $casts = [
        'target_weight' => 'decimal:1',
    ];

    /**
     * この目標体重を持つユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
