<?php

namespace App\Http\Controllers;

use App\Models\Breaktime;

class BreaktimeController extends Controller
{
    /**
     * 指定された休憩レコードを削除する。
     *
     * @param  int $breaktimeId  削除する休憩レコードのID
     * @return \Illuminate\Http\JsonResponse 削除成功時のJSONレスポンス
     */
    public function destroy($breaktimeId)
    {
        $breaktime = Breaktime::findOrFail($breaktimeId);
        $breaktime->delete();

        return response()->json('Breaktime deleted successfully');
    }
}
