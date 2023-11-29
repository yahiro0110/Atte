<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaktime extends Model
{
    use HasFactory;

    // --------------------------------------------------------------------------------
    // モデル属性とリレーションシップ
    // --------------------------------------------------------------------------------

    /**
     * マスアサインメントで使用可能な属性。
     *
     * この属性リストを通じて、createやupdateメソッドなどで一括して割り当て可能なモデルの属性を定義する。
     * - `attendance_id` : 出勤・退勤ID
     * - `start_time` : 休憩開始時刻
     * - `end_time` : 休憩終了時刻
     *
     * @var array
     */
    protected $fillable = ['attendance_id', 'start_time', 'end_time'];

    /**
     * この休憩レコードに関連付けられている勤怠エンティティを取得するリレーションシップ。
     *
     * `Attendance` モデルとの "BelongsTo" 関連を定義する。
     * これにより、特定の休憩レコードに関連付けられている勤怠情報にアクセスできるようになる。
     * （例えば、休憩レコードから直接関連する勤怠の日付や他の属性にアクセスする場合に使用）
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 休憩レコードに関連する勤怠モデルへのリレーションシップ
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
